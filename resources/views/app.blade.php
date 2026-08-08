<!DOCTYPE html>
{{--
    data-theme carries the signed-in driver's stored choice, and is absent for guests and for drivers who
    have never picked one. That absence is what lets the script below fall through to localStorage.
    data-theme-persist tells ThemeContext whether a change is worth writing back to the account.
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @if ($storedTheme = auth()->user()?->theme?->value) data-theme="{{ $storedTheme }}" @endif
    @auth data-theme-persist="1" @endauth>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- Default (light) tint for the iOS status bar / safe area; the script below and then ThemeContext keep it in sync with the active theme. --}}
        <meta name="theme-color" content="#ffffff">

        {{--
            Resolves the theme before first paint: account -> localStorage -> operating system.
            This has to be blocking and un-bundled to beat the first paint, which is why it duplicates a
            few lines of the resolution logic in resources/js/utils/ThemeContext.tsx rather than importing it.
            Without it the dark class only lands after React hydrates, flashing white on every full page load.
        --}}
        <script>
            (function () {
                var el = document.documentElement;
                var theme = el.dataset.theme || localStorage.getItem('theme') || 'system';

                if (theme === 'system') {
                    theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                }

                if (theme === 'dark') {
                    el.classList.add('dark');
                }

                el.style.colorScheme = theme;
                document.querySelector('meta[name="theme-color"]').content = theme === 'dark' ? '#111827' : '#ffffff';
            })();
        </script>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
