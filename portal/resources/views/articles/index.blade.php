@extends('layouts.app')
@section('title', 'Noticias')
@section('content')
<section class="page-hero"><div class="container"><span class="eyebrow eyebrow-dark">Contexto para decidir</span><h1>Noticias que conectan<br>trabajo y futuro.</h1><p>Análisis práctico sobre tecnología, economía, carrera y nuevas formas de trabajar.</p></div></section>
<section class="section-block"><div class="container"><div class="article-grid article-grid-wide">
@foreach($articles as $article)
<article class="article-card"><a class="article-image" href="{{ route('articles.show', $article) }}"><img src="{{ $article->image_url }}" alt="" loading="lazy"><span>{{ $article->category }}</span></a><div class="article-copy"><h3><a href="{{ route('articles.show', $article) }}">{{ $article->title }}</a></h3><p>{{ $article->excerpt }}</p><div class="story-meta dark"><span>{{ $article->published_at->locale('es')->diffForHumans() }}</span><span>4 min</span></div></div></article>
@endforeach
</div></div></section>
@endsection
