@extends('layouts.app')

@section('content')
<section class="hero-section">
    <div class="container">
        <div class="section-kicker"><span>Edición digital</span><span>Actualizada durante el día</span></div>
        <div class="hero-grid">
            @if($featuredArticle)
                <article class="hero-story" style="--story-image: url('{{ $featuredArticle->image_url }}')">
                    <div class="hero-overlay">
                        <span class="eyebrow">{{ $featuredArticle->category }}</span>
                        <h1><a href="{{ route('articles.show', $featuredArticle) }}">{{ $featuredArticle->title }}</a></h1>
                        <p>{{ $featuredArticle->excerpt }}</p>
                        <div class="story-meta"><span>Lectura de 4 min</span><span>{{ $featuredArticle->published_at->locale('es')->diffForHumans() }}</span></div>
                    </div>
                </article>
            @endif
            <aside class="radar-card">
                <div class="radar-heading"><span class="live-dot"></span><iconify-icon icon="ph:trend-up"></iconify-icon><span>Radar laboral</span></div>
                <h2>Lo que mueve el mercado esta semana</h2>
                <div class="radar-item"><strong><iconify-icon icon="ph:lightning"></iconify-icon></strong><div><span>Tecnología</span><p>Automatización e IA aplicada ganan protagonismo.</p></div></div>
                <div class="radar-item"><strong><iconify-icon icon="ph:laptop"></iconify-icon></strong><div><span>Trabajo remoto</span><p>Crece la demanda de perfiles con autonomía demostrable.</p></div></div>
                <div class="radar-item"><strong><iconify-icon icon="ph:graduation-cap"></iconify-icon></strong><div><span>Formación</span><p>Datos y comunicación aparecen como habilidades transversales.</p></div></div>
                <a class="text-link" href="{{ route('jobs.index') }}">Ver oportunidades <iconify-icon icon="ph:arrow-right"></iconify-icon></a>
            </aside>
        </div>
    </div>
</section>

<section class="category-strip">
    <div class="container category-list"><span>Temas</span><a href="#">Tecnología</a><a href="#">Economía</a><a href="#">Carrera</a><a href="#">Trabajo remoto</a><a href="#">Emprendimiento</a></div>
</section>

<section class="section-block">
    <div class="container">
        <div class="section-title"><div><span class="eyebrow eyebrow-dark">Para entender el cambio</span><h2>Historias y herramientas</h2></div><a class="text-link" href="{{ route('articles.index') }}">Todas las noticias <iconify-icon icon="ph:arrow-right"></iconify-icon></a></div>
        <div class="article-grid">
            @foreach($articles as $article)
                <article class="article-card">
                    <a class="article-image" href="{{ route('articles.show', $article) }}"><img src="{{ $article->image_url }}" alt="" loading="lazy"><span>{{ $article->category }}</span></a>
                    <div class="article-copy"><h3><a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a></h3><p>{{ $article->excerpt }}</p><div class="story-meta dark"><span>{{ $article->published_at->locale('es')->diffForHumans() }}</span><span>3 min</span></div></div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="jobs-section">
    <div class="container">
        <div class="section-title light"><div><span class="eyebrow">Oportunidades verificadas</span><h2>Trabajos para dar el siguiente paso</h2></div><a class="button button-light" href="{{ route('jobs.index') }}">Explorar empleos</a></div>
        <div class="job-list">
            @foreach($jobs as $job)
                <a class="job-row" href="{{ route('jobs.show', $job) }}">
                    <span class="company-avatar">{{ strtoupper(substr($job->company, 0, 2)) }}</span>
                    <span class="job-main"><strong>{{ $job->title }}</strong><small>{{ $job->company }} · {{ $job->sector }}</small></span>
                    <span class="job-place"><span><iconify-icon icon="ph:map-pin"></iconify-icon>{{ $job->location }}</span><small>{{ $job->work_mode }}</small></span>
                    <span class="job-salary">{{ $job->salary_label }}</span>
                    <span class="job-arrow"><iconify-icon icon="ph:arrow-up-right"></iconify-icon></span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section class="newsletter" id="newsletter">
    <div class="container newsletter-inner"><div><span class="eyebrow eyebrow-dark"><iconify-icon icon="ph:envelope-simple"></iconify-icon> Un correo, cero ruido</span><h2>Lo esencial para trabajar mejor.</h2><p>Noticias, oportunidades y recursos seleccionados cada semana.</p></div><form class="newsletter-form"><label class="sr-only" for="newsletter-email">Correo electrónico</label><input id="newsletter-email" type="email" placeholder="tu@correo.com"><button type="button">Quiero recibirlo <iconify-icon icon="ph:paper-plane-tilt"></iconify-icon></button></form></div>
</section>
@endsection
