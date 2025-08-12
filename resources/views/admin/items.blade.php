@extends('layouts.admin')

@section('title', 'Items')

@section('content')

<h1>Items</h1>
<section id="items">
    @if ($errors->has('error'))
        <span class="error">{{ $errors->first('error') }}</span>
    @endif
    @each('partials.admin-item', $items, 'item')  
</section>
<div>
    {{ $items->links() }}
</div>
@endsection