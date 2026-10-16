@extends('layouts.app')

@section('title', 'Page not found')

@section('content')
    <x-error-panel icon="compass" title="We couldn't find that page">
        The product may no longer be available, or the link may be mistyped.
    </x-error-panel>
@endsection
