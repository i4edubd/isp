@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{ $router->name }} - Details</h1>
        <h2>IP Pools</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Ranges</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ipPools as $pool)
                    <tr>
                        <td>{{ $pool['name'] }}</td>
                        <td>{{ $pool['ranges'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>PPP Profiles</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Local Address</th>
                    <th>Remote Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pppProfiles as $profile)
                    <tr>
                        <td>{{ $profile['name'] }}</td>
                        <td>{{ $profile['local-address'] ?? '' }}</td>
                        <td>{{ $profile['remote-address'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
