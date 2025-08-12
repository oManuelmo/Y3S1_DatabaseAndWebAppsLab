@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<section id="dashboard">
    <article class="item-state-graph">
        <x-chartjs-component :chart="$chart" />
    </article>
    <article class="dashboard-stats">
        <div class="stat-item">
            <h3>Total Money</h3>
            <p>${{ number_format($totalMoney, 2) }}</p>
        </div>
        <div class="stat-item">
            <h3>Total Bids</h3>
            <p>{{ $totalBids }}</p>
        </div>
    </article>
    <article class="item-category-graph">
        <div class="chart">
            {!! $stylesChart->render() !!}
        </div>
    
        <div class="chart">
            {!! $themesChart->render() !!}
        </div>
    
        <div class="chart">
            {!! $techniquesChart->render() !!}
        </div>
    </article>
</section>

@endsection
