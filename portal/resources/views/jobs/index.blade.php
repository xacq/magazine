@extends('layouts.app')
@section('title', 'Empleos')
@section('content')
<section class="jobs-hero"><div class="container"><div><span class="eyebrow">Oportunidades claras</span><h1>Encuentra trabajo<br>con mejor información.</h1><p>Vacantes organizadas por sector, ubicación y modalidad.</p></div><div class="jobs-count"><strong>{{ $jobs->total() }}</strong><span>oportunidades<br>abiertas ahora</span></div></div></section>
<section class="job-board"><div class="container"><div class="job-board-top"><h2>Vacantes recientes</h2><span>Ordenadas por relevancia y fecha</span></div><div class="job-list job-list-light">
@foreach($jobs as $job)
<a class="job-row" href="{{ route('jobs.show', $job) }}"><span class="company-avatar">{{ strtoupper(substr($job->company, 0, 2)) }}</span><span class="job-main"><strong>{{ $job->title }}</strong><small>{{ $job->company }} · {{ $job->sector }}</small></span><span class="job-place"><span><iconify-icon icon="ph:map-pin"></iconify-icon>{{ $job->location }}</span><small>{{ $job->work_mode }} · {{ $job->employment_type }}</small></span><span class="job-salary">{{ $job->salary_label }}</span><span class="job-arrow"><iconify-icon icon="ph:arrow-up-right"></iconify-icon></span></a>
@endforeach
</div></div></section>
@endsection
