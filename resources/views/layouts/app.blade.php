<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Deesa UCAT AI') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <button type="button" data-theme-toggle class="theme-toggle theme-toggle-floating" aria-label="Toggle dark mode">
        <span class="theme-toggle-track">
            <span class="theme-toggle-thumb"></span>
        </span>
        <span class="theme-toggle-label">Dark mode</span>
    </button>
    <div class="mx-auto min-h-screen max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            @if (! auth()->user()?->isAdmin())
                <div class="glass-card rounded-full px-5 py-3 text-sm text-slate-700">
                    @if (auth()->user()?->hasActivePremium())
                        Premium active
                    @else
                        GBP 30/month premium plan for unlimited AI-generated mock exams
                    @endif
                </div>
            @else
                <div></div>
            @endif
            <div class="flex flex-wrap items-center justify-end gap-3">
                @if (! auth()->user()?->isAdmin())
                    <a href="{{ route('profile.show') }}" class="btn-secondary">Profile</a>
                @endif
                @if (! auth()->user()?->isAdmin() && ! auth()->user()?->hasActivePremium())
                    <a href="{{ route('subscription.show') }}" class="btn-secondary">Premium</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary">Logout</button>
                </form>
            </div>
        </div>
        {{ $slot ?? '' }}
        @yield('content')
    </div>
    @livewireScripts
</body>
</html>
