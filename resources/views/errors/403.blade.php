@extends('layouts.app')

@section('title', 'Not allowed')

@section('content')
    <x-error-panel icon="lock" title="You don't have access to this page">
        If you think this is a mistake, head back to the home page and try again.
    </x-error-panel>
@endsection
