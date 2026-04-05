<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExamManagementController;
use App\Http\Controllers\Admin\NotificationManagementController;
use App\Http\Controllers\Admin\StudentAnalyticsController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ResultController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

Route::get('/', LandingPageController::class)->name('landing');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', function (): View {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request): RedirectResponse {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $target = $request->user()?->isAdmin() ? route('admin.dashboard') : route('dashboard');

        return redirect()->intended($target);
    })->name('login.store');

    Route::get('/register', function (): View {
        return view('auth.register');
    })->name('register');

    Route::get('/forgot-password', function (): View {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('/forgot-password', function (Request $request): RedirectResponse {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = PasswordBroker::sendResetLink(
            $request->only('email')
        );

        return $status === PasswordBroker::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)])->onlyInput('email');
    })->name('password.email');

    Route::get('/reset-password/{token}', function (string $token, Request $request): View {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    })->name('password.reset');

    Route::post('/reset-password', function (Request $request): RedirectResponse {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $status = PasswordBroker::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === PasswordBroker::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]])->onlyInput('email');
    })->name('password.store');

    Route::post('/register', function (Request $request): RedirectResponse {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'avatar_url' => null,
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->isAdmin() ? 'admin.dashboard' : 'dashboard');
    })->name('register.store');
});

Route::post('/logout', function (Request $request): RedirectResponse {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('landing');
})->middleware('auth')->name('logout');

Route::get('/profile', function (Request $request): View {
    return view('profile.show', [
        'user' => $request->user(),
        'price' => SubscriptionService::MONTHLY_PRICE_GBP,
    ]);
})->middleware('auth')->name('profile.show');

Route::post('/profile', function (Request $request): RedirectResponse {
    $user = $request->user();

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
        'avatar_url' => ['nullable', 'url', 'max:2048'],
    ]);

    $user->update($validated);

    return redirect()->route('profile.show')->with('status', 'Profile updated.');
})->middleware('auth')->name('profile.update');

Route::get('/subscription', function (): View {
    return view('subscription.show', [
        'price' => SubscriptionService::MONTHLY_PRICE_GBP,
    ]);
})->middleware('auth')->name('subscription.show');

Route::post('/subscription/activate', function (Request $request, SubscriptionService $subscriptions): RedirectResponse {
    $subscriptions->activateMonthlyPlan($request->user());

    return redirect()->route('subscription.show')->with('status', 'Premium access activated for 30 days.');
})->middleware('auth')->name('subscription.activate');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('exams')->name('exams.')->group(function (): void {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/{exam}', [ExamController::class, 'show'])->name('show');
        Route::post('/{exam}/start', [ExamController::class, 'start'])->name('start');
        Route::get('/attempts/{attempt}', [ExamController::class, 'take'])->name('take');
        Route::post('/attempts/{attempt}/submit', [ExamController::class, 'submit'])->name('submit');
    });

    Route::get('/results/{attempt}', ResultController::class)->name('results.show');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/premium', [UserManagementController::class, 'activatePremium'])->name('users.premium');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('/notifications', [NotificationManagementController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationManagementController::class, 'store'])->name('notifications.store');
    Route::post('/exams/{exam}/toggle-source', [ExamManagementController::class, 'toggleSource'])->name('exams.toggle-source');
    Route::post('/exams/{exam}/regenerate', [ExamManagementController::class, 'regenerate'])->name('exams.regenerate');
    Route::resource('exams', ExamManagementController::class);
    Route::get('/students/{user}', StudentAnalyticsController::class)->name('students.show');
});
