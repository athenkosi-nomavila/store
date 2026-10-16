@extends('layouts.app')

@section('title', 'Page not found')

@section('content')
    <div class="container">
        <div class="empty-state empty-state-page">
            <span class="empty-state-icon"><x-icon name="search" size="40" /></span>
            <p class="eyebrow">Error 404</p>
            <h1>We couldn't find that page</h1>
            <p>The product may have sold out for good or the link may be mistyped.</p>
            <div class="hero-actions">
                <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
                <a href="{{ route('shop.index') }}" class="btn btn-outline">Browse products</a>
            </div>
        </div>
    </div>
@endsection
