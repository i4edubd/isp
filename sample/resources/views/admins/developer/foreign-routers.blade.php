@extends ('laraview.layouts.sideNavLayout')

@section('title')
Routers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection

@section('contentTitle')
<h3>Foreign Routers</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">IP</th>
                    <th scope="col">Location</th>
                    <th scope="col">created at</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($routers as $router )
                <tr>
                    <th scope="row">{{ $router->id }}</th>
                    <td>{{ $router->nasname }}</td>
                    <td>{{ $router->location }}</td>
                    <td>{{ $router->created_at }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
