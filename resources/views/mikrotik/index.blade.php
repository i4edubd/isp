@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>MikroTik Routers</h1>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>IP Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($routers as $router)
                    <tr>
                        <td>{{ $router->name }}</td>
                        <td>{{ $router->ip_address }}</td>
                        <td>
                            <a href="{{ route('mikrotik.show', $router) }}" class="btn btn-primary">View Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
