<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->string('q'));

        $users = User::query()
            ->when($query !== '', function ($builder) use ($query): void {
                $builder->where(function ($nested) use ($query): void {
                    $nested->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->withCount('examAttempts')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'query' => $query,
            'price' => SubscriptionService::MONTHLY_PRICE_GBP,
        ]);
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user->load('examAttempts.exam', 'notifications'),
            'price' => SubscriptionService::MONTHLY_PRICE_GBP,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,student'],
            'subscription_status' => ['required', 'in:free,premium'],
            'avatar_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $user->update([
            ...$validated,
            'role' => UserRole::from($validated['role']),
            'premium_until' => $validated['subscription_status'] === 'premium'
                ? ($user->premium_until?->isFuture() ? $user->premium_until : now()->addMonth())
                : null,
        ]);

        return redirect()->route('admin.users.show', $user)->with('status', 'User updated.');
    }

    public function activatePremium(User $user, SubscriptionService $subscriptions): RedirectResponse
    {
        if ($user->subscription_status === 'premium' && $user->premium_until?->isFuture()) {
            $subscriptions->deactivateMonthlyPlan($user);

            return redirect()->route('admin.users.show', $user)->with('status', 'Premium deactivated.');
        }

        $subscriptions->activateMonthlyPlan($user);

        return redirect()->route('admin.users.show', $user)->with('status', 'Premium activated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('status', 'You cannot delete your own admin account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
