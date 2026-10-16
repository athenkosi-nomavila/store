{{-- Any uncaught exception, and other 5xx codes via 5xx.blade.php. --}}
@extends('layouts.app')

@section('title', 'Something went wrong')

@section('content')
    <x-error-panel icon="alert" title="Something went wrong on our side" retry>
        The problem has been logged. Please try again in a moment.
    </x-error-panel>
@endsection
