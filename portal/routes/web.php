<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobOfferController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/noticias', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/noticias/{article}', [ArticleController::class, 'show'])->name('articles.show');
Route::get('/empleos', [JobOfferController::class, 'index'])->name('jobs.index');
Route::get('/empleos/{jobOffer}', [JobOfferController::class, 'show'])->name('jobs.show');
