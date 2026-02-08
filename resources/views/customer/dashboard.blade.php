@extends('layouts.app') {{-- Assuming a main layout file exists --}}

@section('content')
<div class="container">
    <h1>Customer Dashboard</h1>
    <p>Welcome, {{ Auth::user()->name }}!</p>

    @if(isset($customer))
        <h2>Account Details</h2>
        <p><strong>Username:</strong> {{ $customer->username }}</p>
        <p><strong>Status:</strong> {{ $customer->status }}</p>
        <p><strong>Package:</strong> {{ $customer->package }}</p>
        <p><strong>Expires on:</strong> {{ $customer->expiration }}</p>
    @else
        <p>No customer account linked to this user.</p>
    @endif
</div>
@endsection
