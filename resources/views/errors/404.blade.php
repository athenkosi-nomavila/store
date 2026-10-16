@extends('layouts.app')

@section('title', 'Page not found')

@section('content')
    <div class="container">
        <div class="empty-state">
            <h1>We couldn't find that page</h1>
            <p>The product may no longer be available, or the link may be mistyped.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
        </div>
    </div>
@endsection
