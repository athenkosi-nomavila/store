@extends('layouts.app')

@section('title', 'About')

@section('content')
    <div class="container prose-page">
        <h1 class="listing-title">About Store</h1>
        <p class="lead">Store is a small, independent shop for everyday essentials. We pick a short list of products we would happily use ourselves, and we keep it that way.</p>

        <h2 id="delivery">Delivery</h2>
        <p>Orders over R 500 ship free anywhere in South Africa. Most orders arrive within three to five working days.</p>

        <h2 id="returns">Returns</h2>
        <p>Changed your mind? Return any unused item within 30 days for a full refund.</p>

        <p><a href="{{ route('shop.index') }}" class="btn btn-primary">Browse the shop</a></p>
    </div>
@endsection
