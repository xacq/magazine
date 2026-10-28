<?php

namespace App\Http\Controllers;

use App\Models\JobOffer;
use Illuminate\View\View;

class JobOfferController extends Controller
{
    public function index(): View
    {
        $jobs = JobOffer::query()
            ->where('status', 'open')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->paginate(12);

        return view('jobs.index', compact('jobs'));
    }

    public function show(JobOffer $jobOffer): View
    {
        abort_unless($jobOffer->status === 'open', 404);

        return view('jobs.show', ['job' => $jobOffer]);
    }
}
