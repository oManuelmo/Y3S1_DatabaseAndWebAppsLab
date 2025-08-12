@extends('layouts.admin')

@section('content')
<h1>Manage {{ ucfirst($type) }}</h1>
<section id="category">

    <div id="categoryContainer" data-type="{{ $type }}">
        <input id="newValue" type="text" placeholder="Add new {{ $type }}" class="form-control" />
        <button onclick="addCategory()" class="btn btn-primary mt-2">Add</button>
    </div>

    @each('partials.admin-category-item', $items, 'item')
</section>
<div>
    {{ $items->links() }}
</div>
@endsection
