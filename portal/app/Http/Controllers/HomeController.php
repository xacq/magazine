<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\JobOffer;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $featuredArticle = Article::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->latest('published_at')
            ->first();

        $articles = Article::query()
            ->where('status', 'published')
            ->when($featuredArticle, fn ($query) => $query->whereKeyNot($featuredArticle->getKey()))
            ->latest('published_at')
            ->limit(4)
            ->get();

        $jobs = JobOffer::query()
            ->where('status', 'open')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(5)
            ->get();

        return view('home', compact('featuredArticle', 'articles', 'jobs'));
    }
}
