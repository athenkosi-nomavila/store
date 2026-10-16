@extends('layouts.app')

@section('title', 'Too many requests')

@section('content')
    <x-error-panel icon="clock" title="Slow down a little">
        We've had a lot of requests from you in a short time. Please wait a minute and try again.
    </x-error-panel>
@endsection
