<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TryoutAccess;
use App\Models\TryoutPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TryoutAccessService
{
    private const SESSION_KEY = 'public_tryout_access_emails';

    public function hasSessionAccess(Request $request, TryoutPackage $tryoutPackage): bool
    {
        if ($tryoutPackage->isFreePackage()) {
            return true;
        }

        $email = $this->getRememberedEmail($request, $tryoutPackage);

        if (blank($email)) {
            return false;
        }

        return $this->findActiveAccessForPackage($tryoutPackage, $email) !== null;
    }

    public function getRememberedEmail(Request $request, TryoutPackage $tryoutPackage): ?string
    {
        $cached = $request->session()->get(self::SESSION_KEY, []);

        return isset($cached[$tryoutPackage->id]) && filled($cached[$tryoutPackage->id])
            ? (string) $cached[$tryoutPackage->id]
            : null;
    }

    public function rememberAccess(Request $request, TryoutPackage $tryoutPackage, string $email): void
    {
        $cached = $request->session()->get(self::SESSION_KEY, []);
        $cached[$tryoutPackage->id] = $this->normalizeEmail($email);

        $request->session()->put(self::SESSION_KEY, $cached);
    }

    public function findActiveAccessForPackage(TryoutPackage $tryoutPackage, ?string $email): ?TryoutAccess
    {
        if ($tryoutPackage->isFreePackage() || blank($email)) {
            return null;
        }

        $email = $this->normalizeEmail($email);

        $access = TryoutAccess::query()
            ->where('tryout_package_id', $tryoutPackage->id)
            ->where('buyer_email', $email)
            ->currentlyActive()
            ->latest('expires_at')
            ->latest('id')
            ->first();

        if ($access) {
            return $access;
        }

        $order = $this->findPaidOrderForPackage($tryoutPackage, $email);

        if (! $order) {
            return null;
        }

        $existingOrderAccess = TryoutAccess::query()
            ->where('tryout_package_id', $tryoutPackage->id)
            ->where('order_id', $order->id)
            ->where('buyer_email', $email)
            ->latest('id')
            ->first();

        if ($existingOrderAccess) {
            return $existingOrderAccess->isCurrentlyActive() ? $existingOrderAccess : null;
        }

        $startsAt = $order->paid_at ?? $order->created_at ?? now();
        $expiresAt = $tryoutPackage->access_days
            ? $startsAt->copy()->addDays((int) $tryoutPackage->access_days)
            : null;

        $access = TryoutAccess::query()->updateOrCreate(
            [
                'tryout_package_id' => $tryoutPackage->id,
                'order_id' => $order->id,
                'buyer_email' => $email,
            ],
            [
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'remaining_attempts' => max(1, (int) ($tryoutPackage->attempt_limit ?? 1)),
                'is_active' => true,
            ]
        );

        return $access->isCurrentlyActive() ? $access : null;
    }

    public function resolvePackageFromOrder(Order $order): ?TryoutPackage
    {
        $productSlug = $order->product?->slug;

        if (blank($productSlug)) {
            return null;
        }

        return TryoutPackage::query()
            ->where('slug', $productSlug)
            ->where('price', '>', 0)
            ->first();
    }

    public function ensureAccessFromPaidOrder(Order $order): ?TryoutAccess
    {
        if (! $order->isPaid()) {
            return null;
        }

        $tryoutPackage = $this->resolvePackageFromOrder($order);

        if (! $tryoutPackage || blank($order->buyer_email)) {
            return null;
        }

        $email = $this->normalizeEmail($order->buyer_email);

        $existingAccess = TryoutAccess::query()
            ->where('tryout_package_id', $tryoutPackage->id)
            ->where('order_id', $order->id)
            ->where('buyer_email', $email)
            ->latest('id')
            ->first();

        $startsAt = $order->paid_at ?? $order->created_at ?? now();
        $expiresAt = $tryoutPackage->access_days
            ? $startsAt->copy()->addDays((int) $tryoutPackage->access_days)
            : null;

        $remainingAttempts = $existingAccess?->remaining_attempts;

        if ($remainingAttempts === null) {
            $remainingAttempts = max(1, (int) ($tryoutPackage->attempt_limit ?? 1));
        }

        return TryoutAccess::query()->updateOrCreate(
            [
                'tryout_package_id' => $tryoutPackage->id,
                'order_id' => $order->id,
                'buyer_email' => $email,
            ],
            [
                'starts_at' => $startsAt,
                'expires_at' => $expiresAt,
                'remaining_attempts' => $remainingAttempts,
                'is_active' => $remainingAttempts > 0,
            ]
        );
    }

    public function consumeAttempt(TryoutAccess $tryoutAccess): TryoutAccess
    {
        $lockedAccess = TryoutAccess::query()
            ->whereKey($tryoutAccess->id)
            ->lockForUpdate()
            ->firstOrFail();

        $remainingAttempts = max(0, (int) $lockedAccess->remaining_attempts - 1);

        $lockedAccess->forceFill([
            'remaining_attempts' => $remainingAttempts,
            'is_active' => $remainingAttempts > 0 && $lockedAccess->is_active,
        ])->save();

        return $lockedAccess->fresh();
    }

    private function findPaidOrderForPackage(TryoutPackage $tryoutPackage, string $email): ?Order
    {
        $product = $tryoutPackage->checkoutProduct();

        if (! $product) {
            return null;
        }

        return Order::query()
            ->where('product_id', $product->id)
            ->whereRaw('LOWER(buyer_email) = ?', [$email])
            ->where('status', Order::STATUS_PAID)
            ->latest('paid_at')
            ->latest('id')
            ->first();
    }

    private function normalizeEmail(string $email): string
    {
        return Str::lower(trim($email));
    }
}
