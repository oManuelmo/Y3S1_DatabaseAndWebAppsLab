@extends('layouts.admin')

@section('title', 'Items')

@section('content')

<h1>Pending items</h1>
<section id="pending-items">
    @each('partials.admin-pending-item', $items, 'item')  
</section>
<div>
    {{ $items->links() }}
</div>
@endsection