<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), config('localization.rtl', []), true) ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#0A0C10">
        <meta name="color-scheme" content="dark light">

        <title inertia>{{ config('app.name', 'Bookly') }}</title>

        @php
            $ogTitle = __('app.meta.title');
            $ogDescription = __('app.meta.description');
            $ogSiteName = __('app.meta.site_name');
            $ogImage = 'https://demo-booking.dev4rweb.com/og-image.jpg';
            $ogUrl = url()->current();
        @endphp
        <meta name="description" content="{{ $ogDescription }}">
        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $ogDescription }}">
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:url" content="{{ $ogUrl }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $ogSiteName }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $ogTitle }}">
        <meta name="twitter:description" content="{{ $ogDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="icon" href="/favicon-32x32.png" type="image/png" sizes="32x32">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" sizes="180x180">
        <link rel="manifest" href="/site.webmanifest">

        <script>
            (function () {
                var theme = 'dark';
                try {
                    var stored = localStorage.getItem('theme');
                    if (stored === 'light' || stored === 'dark') {
                        theme = stored;
                    }
                } catch (e) {}
                document.documentElement.classList.add(theme);
                document.documentElement.style.colorScheme = theme;
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
