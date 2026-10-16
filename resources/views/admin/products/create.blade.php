@extends('layouts.admin')

@section('title', 'Add product')

@section('content')
    @include('admin.products._form', ['product' => null])
@endsection
