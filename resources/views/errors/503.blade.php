{{-- Maintenance mode (php artisan down) and database outages. --}}
@extends('layouts.app')

@section('title', 'Back soon')

@section('content')
    <x-error-panel icon="clock" title="We'll be back shortly" retry>
        The store is briefly unavailable. Please try again in a minute.
    </x-error-panel>
@endsection
