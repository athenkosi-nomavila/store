@extends('layouts.app')

@section('title', 'Page expired')

@section('content')
    <x-error-panel icon="refresh" title="This page has expired">
        It was open for a while, so for your security it needs a refresh. Please go back and try again.
    </x-error-panel>
@endsection
