@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Failed Logins
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '0';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.developer.sidebar')
@endsection

@section('contentTitle')
    <h3>Failed Logins</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <table id="data_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" style="width: 2%">#</th>
                        <th scope="col">guard</th>
                        <th scope="col">email</th>
                        <th scope="col">password</th>
                        <th scope="col">created at</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($failed_logins as $failed_login)
                        <tr>
                            <th scope="row">{{ $failed_login->id }}</th>
                            <td>{{ $failed_login->guard }}</td>
                            <td>{{ $failed_login->email }}</td>
                            <td>{{ $failed_login->password }}</td>
                            <td>{{ $failed_login->created_at }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>
    </div>
@endsection

@section('pageJs')
@endsection
