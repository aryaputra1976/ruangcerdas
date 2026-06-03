<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TryoutPackage;
use App\Models\TryoutSession;
use App\Services\TryoutAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class TryoutController extends Controller
{
    public function index(Request $request, TryoutAccessService $tryoutAccessService)
    {
        $packages = TryoutPackage::query()
            ->active()
            ->orderByDesc('is_free')
            ->orderBy('price')
            ->latest()
            ->get();

        $hasTryoutHistory = ! empty($request->session()->get('public_tryout_session_ids', []));
        $packageStates = $packages->mapWithKeys(function (TryoutPackage $package) use ($request, $tryoutAccessService) {
            $hasAccess = $tryoutAccessService->hasSessionAccess($request, $package);

            return [
                $package->id => [
                    'hasAccess' => $hasAccess,
                    'canStart' => $package->is_free || $hasAccess,
                    'buyUrl' => route('public.tryouts.buy', $package),
                ],
            ];
        });

        return view('public.tryouts.index', compact('packages', 'hasTryoutHistory', 'packageStates'));
    }

    public function start(Request $request, TryoutPackage $tryoutPackage, TryoutAccessService $tryoutAccessService)
    {
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

    public function buy(TryoutPackage $tryoutPackage): RedirectResponse
    {
        abort_unless($tryoutPackage->is_active, 404);

        if ($tryoutPackage->is_free) {
            return redirect()->route('public.tryouts.start', $tryoutPackage);
        }

        $product = $tryoutPackage->checkoutProduct();

        if ($product && $product->isVisibleToPublic()) {
            return redirect()->route('checkout.create', $product);
        }

        return redirect()
            ->route('products.index')
            ->with('error', 'Silakan beli paket tryout untuk membuka akses.');
    }
}
