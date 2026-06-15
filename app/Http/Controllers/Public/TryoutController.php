<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use App\Models\TryoutPackage;
use App\Models\TryoutSession;
use App\Support\TryoutBlueprint;
use App\Services\TryoutAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class TryoutController extends Controller
{
    public function hub()
    {
        $cards = [
            [
                'title' => 'Tryout CPNS',
                'description' => 'Paket latihan CPNS yang sudah aktif dan bisa langsung dipilih.',
                'url' => route('public.tryouts.index'),
                'badge' => $this->tryoutPackageCount(TryoutBlueprint::TYPE_CPNS) > 0 ? null : 'Segera Hadir',
                'cta' => 'Lihat Paket',
            ],
            [
                'title' => 'Tryout PPPK',
                'description' => 'Paket tryout PPPK dengan fondasi yang terpisah dari CPNS agar alur soal dan penilaiannya tetap rapi.',
                'url' => route('public.tryouts.pppk'),
                'badge' => $this->tryoutPackageCount(TryoutBlueprint::TYPE_PPPK) > 0 ? null : 'Segera Hadir',
                'cta' => 'Buka Kategori',
            ],
            [
                'title' => 'Tryout PPPK Tendik',
                'description' => 'Ruang khusus tryout PPPK Tendik untuk membantu latihan yang lebih terarah.',
                'url' => route('public.tryouts.pppk-tendik'),
                'badge' => $this->tryoutPackageCount(TryoutBlueprint::TYPE_PPPK_TENDIK) > 0 ? null : 'Segera Hadir',
                'cta' => 'Buka Kategori',
            ],
        ];

        return view('public.tryouts.hub', compact('cards'));
    }

    public function index(Request $request, TryoutAccessService $tryoutAccessService)
    {
        return $this->packageListing($request, $tryoutAccessService, TryoutBlueprint::TYPE_CPNS);
    }

    public function pppk(Request $request, TryoutAccessService $tryoutAccessService)
    {
        return $this->packageListing($request, $tryoutAccessService, TryoutBlueprint::TYPE_PPPK);
    }

    public function pppkTendik(Request $request, TryoutAccessService $tryoutAccessService)
    {
        return $this->packageListing($request, $tryoutAccessService, TryoutBlueprint::TYPE_PPPK_TENDIK);
    }

    public function start(Request $request, string $tryoutType, TryoutPackage $tryoutPackage, TryoutAccessService $tryoutAccessService)
    {
        $this->ensurePackageTypeMatchesRoute($tryoutPackage, $tryoutType);
        abort_unless($tryoutPackage->is_active, 404);

        $recentPackageSession = TryoutSession::query()
            ->with('package')
            ->whereIn('id', $request->session()->get('public_tryout_session_ids', []))
            ->where('tryout_package_id', $tryoutPackage->id)
            ->latest('started_at')
            ->first();

        $rememberedAccessEmail = $tryoutAccessService->getRememberedEmail($request, $tryoutPackage);
        $hasSessionAccess = $tryoutAccessService->hasSessionAccess($request, $tryoutPackage);

        return view('public.tryouts.start', compact(
            'tryoutPackage',
            'recentPackageSession',
            'rememberedAccessEmail',
            'hasSessionAccess',
        ));
    }

    public function buy(string $tryoutType, TryoutPackage $tryoutPackage): RedirectResponse
    {
        $this->ensurePackageTypeMatchesRoute($tryoutPackage, $tryoutType);
        abort_unless($tryoutPackage->is_active, 404);

        if ($tryoutPackage->isFreePackage()) {
            return redirect()->route('public.tryouts.packages.start', [
                'tryoutType' => $tryoutPackage->routeSegment(),
                'tryoutPackage' => $tryoutPackage->slug,
            ]);
        }

        $product = $tryoutPackage->checkoutProduct();

        if ($product) {
            return redirect()->route('checkout.create', $product->slug);
        }

        return redirect()
            ->route($tryoutPackage->listingRouteName())
            ->with('error', 'Paket tryout ini belum siap dibuka ke checkout. Coba lagi sebentar lagi.');
    }

    private function packageListing(Request $request, TryoutAccessService $tryoutAccessService, string $type)
    {
        $packages = TryoutPackage::query()
            ->active()
            ->ofTryoutType($type)
            ->orderByRaw('CASE WHEN COALESCE(price, 0) <= 0 OR is_free = 1 THEN 1 ELSE 0 END DESC')
            ->orderBy('price')
            ->latest()
            ->get()

            ;

        $hasTryoutHistory = ! empty($request->session()->get('public_tryout_session_ids', []));
        $packageStates = $packages->mapWithKeys(function (TryoutPackage $package) use ($request, $tryoutAccessService) {
            $hasAccess = $tryoutAccessService->hasSessionAccess($request, $package);
            $isFreePackage = $package->isFreePackage();

            return [
                $package->id => [
                    'hasAccess' => $hasAccess,
                    'canStart' => $isFreePackage || $hasAccess,
                    'buyUrl' => route('public.tryouts.packages.buy', [
                        'tryoutType' => $package->routeSegment(),
                        'tryoutPackage' => $package->slug,
                    ]),
                    'startUrl' => route('public.tryouts.packages.start', [
                        'tryoutType' => $package->routeSegment(),
                        'tryoutPackage' => $package->slug,
                    ]),
                ],
            ];
        });

        return view('public.tryouts.index', [
            'packages' => $packages,
            'hasTryoutHistory' => $hasTryoutHistory,
            'packageStates' => $packageStates,
            'pageTitle' => TryoutBlueprint::typeLabel($type),
            'pageMetaDescription' => $this->pageMetaDescription($type),
            'pageHeading' => 'Daftar Paket ' . TryoutBlueprint::typeLabel($type),
            'pageDescription' => $this->pageDescription($type),
            'pageBackUrl' => route('public.tryouts.hub'),
            'pageHistoryUrl' => route('public.tryout-sessions.history'),
            'pageRouteName' => match ($type) {
                TryoutBlueprint::TYPE_PPPK => 'public.tryouts.pppk',
                TryoutBlueprint::TYPE_PPPK_TENDIK => 'public.tryouts.pppk-tendik',
                default => 'public.tryouts.index',
            },
        ]);
    }

    private function tryoutPackageCount(string $type): int
    {
        return TryoutPackage::query()
            ->active()
            ->ofTryoutType($type)
            ->count();
    }

    private function ensurePackageTypeMatchesRoute(TryoutPackage $tryoutPackage, string $routeSegment): void
    {
        abort_unless($tryoutPackage->routeSegment() === $routeSegment, 404);
    }

    private function pageDescription(string $type): string
    {
        return match ($type) {
            TryoutBlueprint::TYPE_PPPK => 'Latihan tryout PPPK dengan komposisi section yang disiapkan khusus untuk jalur PPPK.',
            TryoutBlueprint::TYPE_PPPK_TENDIK => 'Pilih paket tryout PPPK Tendik yang aktif untuk latihan yang lebih terarah.',
            default => 'Pilih paket tryout CPNS aktif yang paling sesuai untuk target belajarmu.',
        };
    }

    private function pageMetaDescription(string $type): string
    {
        return match ($type) {
            TryoutBlueprint::TYPE_PPPK => 'Daftar paket tryout PPPK online Ruang Cerdas.',
            TryoutBlueprint::TYPE_PPPK_TENDIK => 'Daftar paket tryout PPPK Tendik online Ruang Cerdas.',
            default => 'Daftar paket tryout CPNS online Ruang Cerdas.',
        };
    }
}
