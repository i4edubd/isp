@extends('layouts.developer')

@section('content')
    <h1>Developer Dashboard</h1>
    <p>Welcome to the Developer Panel. Here you can manage the system architecture and deployments.</p>
    <p>System Status: {{ $status }}</p>
@endsection
