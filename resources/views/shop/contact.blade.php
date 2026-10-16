@extends('layouts.app')

@section('title', 'Contact')

@section('content')
    <div class="container prose-page">
        <h1 class="listing-title">Contact us</h1>
        <p class="lead">Questions about an order or a product? Our Cape Town team is happy to help, Monday to Friday, 8am to 5pm.</p>
        <ul class="contact-list">
            <li><x-icon name="mail" /> <span>hello@store.example</span></li>
            <li><x-icon name="phone" /> <span>021 555 0100</span></li>
            <li><x-icon name="pin" /> <span>12 Bree Street, Cape Town, 8001</span></li>
        </ul>
    </div>
@endsection
