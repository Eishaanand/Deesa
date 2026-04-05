<?php

namespace App\Services;

use App\Models\User;

class SubscriptionService
{
    public const MONTHLY_PRICE_GBP = 30;

    public function activateMonthlyPlan(User $user): void
    {
        $user->forceFill([
            'subscription_status' => 'premium',
            'premium_until' => now()->addMonth(),
        ])->save();
    }

    public function deactivateMonthlyPlan(User $user): void
    {
        $user->forceFill([
            'subscription_status' => 'free',
            'premium_until' => null,
        ])->save();
    }
}
