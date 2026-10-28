<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Noticias útiles y oportunidades laborales para profesionales en Ecuador.">
    <title>@yield('title', 'Pulso Laboral') · Noticias y empleo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,600;6..72,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
</head>
<body>
    <div class="topline">
        <div class="container topline-inner">
            <span>{{ now()->locale('es')->translatedFormat('l, j \d\e F \d\e Y') }}</span>
            <span class="topline-message">Información para avanzar · Empleo para crecer</span>
        </div>
    </div>
    <header class="site-header">
        <div class="container header-main">
            <a class="brand" href="{{ route('home') }}" aria-label="Pulso Laboral, inicio">
                <span class="brand-mark">PL</span>
                <span><strong>Pulso</strong><em>Laboral</em></span>
            </a>
            <nav class="main-nav" aria-label="Navegación principal">
                <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Portada</a>
                <a class="{{ request()->routeIs('articles.*') ? 'active' : '' }}" href="{{ route('articles.index') }}">Noticias</a>
                <a class="{{ request()->routeIs('jobs.*') ? 'active' : '' }}" href="{{ route('jobs.index') }}">Empleos</a>
                <a href="#newsletter">Boletín</a>
            </nav>
            <a class="button button-dark header-cta" href="{{ route('jobs.index') }}">Publicar vacante</a>
        </div>
    </header>

    <main>@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="brand brand-light" href="{{ route('home') }}"><span class="brand-mark">PL</span><span><strong>Pulso</strong><em>Laboral</em></span></a>
                <p>Noticias útiles, oportunidades verificadas y herramientas para avanzar profesionalmente.</p>
            </div>
            <div><h3>Explorar</h3><a href="{{ route('articles.index') }}">Noticias</a><a href="{{ route('jobs.index') }}">Empleos</a><a href="#newsletter">Boletín</a></div>
            <div><h3>Proyecto</h3><a href="#">Sobre nosotros</a><a href="#">Fuentes y metodología</a><a href="#">Contacto</a></div>
        </div>
        <div class="container footer-bottom">
            <span>© {{ now()->year }} Pulso Laboral. Prototipo en desarrollo.</span>
            <span>Hecho en Ecuador.</span>
        </div>
    </footer>
</body>
</html>
