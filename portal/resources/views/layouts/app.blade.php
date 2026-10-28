<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Magazine by GreenPC: noticias, tecnología y oportunidades laborales para Ecuador.">
    <meta name="theme-color" content="#13213c">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_EC">
    <meta property="og:site_name" content="Magazine by GreenPC">
    <meta property="og:title" content="@yield('title', 'Magazine by GreenPC')">
    <meta property="og:description" content="Magazine by GreenPC: noticias, tecnología y oportunidades laborales para Ecuador.">
    <meta property="og:image" content="{{ asset('images/magazine-greenpc-logo-v1.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Magazine by GreenPC')">
    <meta name="twitter:description" content="Magazine by GreenPC: noticias, tecnología y oportunidades laborales para Ecuador.">
    <meta name="twitter:image" content="{{ asset('images/magazine-greenpc-logo-v1.png') }}">
    <title>@yield('title', 'Magazine by GreenPC') · Ideas, tecnología y empleo</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('images/magazine-greenpc-logo-v1.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,600;6..72,700&display=swap" rel="stylesheet">
    <script defer src="{{ asset('vendor/iconify/iconify-icon.min.js') }}"></script>
    <script defer src="{{ asset('js/icons.js') }}" data-icon-collection="{{ asset('vendor/iconify/ph-icons.json') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
</head>
<body>
    <div class="topline">
        <div class="container topline-inner">
            <span>{{ now()->locale('es')->translatedFormat('l, j \d\e F \d\e Y') }}</span>
            <span class="topline-message"><iconify-icon icon="@local:ph:globe-hemisphere-west"></iconify-icon> Ecuador · Tecnología, economía y oportunidades</span>
        </div>
    </div>
    <header class="site-header">
        <div class="container header-main">
            <a class="brand" href="{{ route('home') }}" aria-label="Magazine by GreenPC, inicio">
                <img class="brand-logo" src="{{ asset('images/magazine-greenpc-logo-v1.png') }}" alt="">
            </a>
            <nav class="main-nav" aria-label="Navegación principal">
                <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}"><iconify-icon icon="@local:ph:house-line"></iconify-icon><span>Portada</span></a>
                <a class="{{ request()->routeIs('articles.*') ? 'active' : '' }}" href="{{ route('articles.index') }}"><iconify-icon icon="@local:ph:newspaper"></iconify-icon><span>Noticias</span></a>
                <a class="{{ request()->routeIs('jobs.*') ? 'active' : '' }}" href="{{ route('jobs.index') }}"><iconify-icon icon="@local:ph:briefcase"></iconify-icon><span>Empleos</span></a>
                <a href="#newsletter"><iconify-icon icon="@local:ph:envelope-simple"></iconify-icon><span>Boletín</span></a>
            </nav>
            <a class="button button-dark header-cta" href="{{ route('jobs.index') }}"><iconify-icon icon="@local:ph:plus-circle"></iconify-icon> Publicar vacante</a>
        </div>
    </header>

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="brand brand-light" href="{{ route('home') }}" aria-label="Magazine by GreenPC, inicio">
                    <img class="brand-logo brand-logo-footer" src="{{ asset('images/magazine-greenpc-logo-v1.png') }}" alt="Magazine by GreenPC">
                </a>
                <p>Periodismo útil sobre tecnología, economía, carrera y las oportunidades que mueven Ecuador.</p>
            </div>
            <div><h3>Explorar</h3><a href="{{ route('articles.index') }}">Noticias</a><a href="{{ route('jobs.index') }}">Empleos</a><a href="#newsletter">Boletín</a></div>
            <div><h3>Proyecto</h3><a href="#">Sobre nosotros</a><a href="#">Fuentes y metodología</a><a href="#">Contacto</a></div>
        </div>
        <div class="container footer-bottom">
            <span>© {{ now()->year }} Magazine by GreenPC. Prototipo en desarrollo.</span>
            <span>Hecho en Ecuador.</span>
        </div>
    </footer>
</body>
</html>
