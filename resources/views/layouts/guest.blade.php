<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Deesa UCAT AI') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-x-hidden">
    <button type="button" data-theme-toggle class="theme-toggle theme-toggle-floating" aria-label="Toggle dark mode">
        <span class="theme-toggle-track">
            <span class="theme-toggle-thumb"></span>
        </span>
        <span class="theme-toggle-label">Dark mode</span>
    </button>
    @yield('content')
</body>
</html>
