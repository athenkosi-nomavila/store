@extends('layouts.app')

@section('title', 'Page not found')

@section('content')
    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        <div class="mx-auto max-w-md rounded-xl border border-dashed border-zinc-300 bg-white p-10 text-center">
            <h1 class="text-xl font-bold">We couldn't find that page</h1>
            <p class="mt-2 text-zinc-500">The product may no longer be available, or the link may be mistyped.</p>
            <a href="{{ route('home') }}" class="mt-5 inline-flex h-11 items-center rounded-full bg-brand-600 px-6 font-semibold text-white hover:bg-brand-700">Back to home</a>
        </div>
    </div>
@endsection
