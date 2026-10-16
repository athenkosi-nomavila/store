{{-- Client errors without their own page (405 and similar). --}}
@extends('layouts.app')

@section('title', 'Request not completed')

@section('content')
    <x-error-panel icon="alert" title="We couldn't complete that request">
        Please go back and try again.
    </x-error-panel>
@endsection
