<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="/storage/favicon.png" type="image/png">

    <!-- GOOGLE TAG UNIFIÉ (GA4 + ADS) -->
    @if(config('services.google.analytics_id') || config('services.google.ads_id'))
        <!-- On charge le script avec le premier ID disponible -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') ?? config('services.google.ads_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer ||[];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            // Initialisation GA4
            @if(config('services.google.analytics_id'))
                gtag('config', '{{ config('services.google.analytics_id') }}');
            @endif

            // Initialisation Google Ads
            @if(config('services.google.ads_id'))
                gtag('config', '{{ config('services.google.ads_id') }}');
            @endif
        </script>
    @endif

    <!-- MICROSOFT CLARITY -->
    @if(config('services.microsoft.clarity_id'))
        <script type="text/javascript">
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "{{ config('services.microsoft.clarity_id') }}");
        </script>
    @endif

    <!-- Scripts Laravel/Vite -->
    @routes
    @vite(['resources/js/app.ts', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>