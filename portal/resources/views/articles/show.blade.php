@extends('layouts.app')
@section('title', $article->title)
@section('content')
<article class="article-detail">
    <header class="article-detail-header"><div class="container narrow"><span class="eyebrow eyebrow-dark">{{ $article->category }}</span><h1>{{ $article->title }}</h1><p class="lead">{{ $article->excerpt }}</p><div class="article-byline"><span><iconify-icon icon="@local:ph:pen-nib"></iconify-icon> Equipo editorial</span><span><iconify-icon icon="@local:ph:calendar-blank"></iconify-icon> {{ $article->published_at->locale('es')->translatedFormat('j \d\e F, Y · H:i') }}</span><span><iconify-icon icon="@local:ph:clock"></iconify-icon> Lectura de 4 min</span></div></div></header>
    @if($article->image_url)<div class="container article-detail-image"><img src="{{ $article->image_url }}" alt=""></div>@endif
    <div class="container narrow article-body">{!! $article->body !!}<aside class="source-note"><strong><iconify-icon icon="@local:ph:seal-check"></iconify-icon> Transparencia editorial</strong><p>Este contenido forma parte del prototipo. La versión final mostrará aquí las fuentes, autoría y metodología de verificación.</p></aside></div>
</article>
@endsection
