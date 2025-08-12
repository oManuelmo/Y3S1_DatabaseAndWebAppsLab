@extends('layouts.admin')

@section('title', 'Reports')

@section('content')

<h1>Reports</h1>
<section id="reports">
    @forelse($reports as $report)
        @include('partials.admin-report', ['report' => $report])
    @empty
        <p class="text-center">No reports found.</p>
    @endforelse
</section>
<div>
    {{ $reports->links() }}
</div>
@endsection
