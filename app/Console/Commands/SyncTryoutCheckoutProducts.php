<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\TryoutPackage;
use Illuminate\Console\Command;

class SyncTryoutCheckoutProducts extends Command
{
    protected $signature = 'tryout-packages:sync-products {--cleanup : Nonaktifkan produk tryout yang tidak lagi punya paket premium aktif}';

    protected $description = 'Sinkronkan paket tryout ke produk checkout publik.';

    public function handle(): int
    {
        $synced = 0;
        $retired = 0;

        TryoutPackage::withTrashed()
            ->orderBy('id')
            ->chunkById(100, function ($packages) use (&$synced, &$retired) {
                foreach ($packages as $package) {
                    if ($package->trashed() || ! $package->is_active || $package->isFreePackage()) {
                        $retired += Product::query()
                            ->where('slug', $package->slug)
                            ->where('product_type', 'tryout')
                            ->update([
                                'is_active' => false,
                                'deleted_at' => now(),
                            ]);

                        continue;
                    }

                    $package->checkoutProduct();
                    $synced++;
                }
            });

        if ($this->option('cleanup')) {
            $activePremiumSlugs = TryoutPackage::query()
                ->where('is_active', true)
                ->get()
                ->reject(fn (TryoutPackage $package) => $package->isFreePackage())
                ->pluck('slug')
                ->filter()
                ->all();

            $cleanupQuery = Product::query()
                ->where('product_type', 'tryout');

            if ($activePremiumSlugs !== []) {
                $cleanupQuery->whereNotIn('slug', $activePremiumSlugs);
            }

            $retired += $cleanupQuery->update([
                'is_active' => false,
                'deleted_at' => now(),
            ]);
        }

        $this->info("Sinkron selesai. Produk aktif diperbarui: {$synced}. Produk tryout dinonaktifkan: {$retired}.");

        return self::SUCCESS;
    }
}
