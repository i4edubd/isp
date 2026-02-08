@extends ('laraview.layouts.sideNavLayout')

@section('title')
Backup Settings
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '9';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')

<ul class="nav flex-column flex-sm-row">
    <!--New Setting-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('backup_settings.create') }}">
            <i class="fas fa-plus"></i>
            New Setting
        </a>
    </li>
    <!--/New Setting-->
</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link mb-2" href="#">Backup & Authenticator Settings : </a>
            </li>
        </ul>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Operator</th>
                    <th scope="col">Router</th>
                    <th scope="col">Primary Authenticator</th>
                    <th scope="col">Backup Type</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($backup_settings as $backup_setting)
                <tr>
                    <th scope="row">{{ $backup_setting->id }}</th>
                    <td>{{ $backup_setting->operator->name }}</td>
                    <td>{{ $backup_setting->nas_ip }}</td>
                    <td>{{ $backup_setting->primary_authenticator }}</td>
                    <td>{{ $backup_setting->backup_type }}</td>

                    <td class="d-sm-flex">

                        <a class="btn btn-primary btn-sm mr-2"
                            href="{{ route('backup_settings.customer_backup_request.create', ['backup_setting' => $backup_setting->id]) }}"
                            role="button">Backup Now
                        </a>

                        <a class="btn btn-primary btn-sm mr-2"
                            href="{{ route('backup_settings.edit', ['backup_setting' => $backup_setting->id]) }}"
                            role="button">Edit
                        </a>

                        <form method="post"
                            action="{{ route('backup_settings.destroy', ['backup_setting' => $backup_setting->id]) }}"
                            onsubmit="return confirm('Are you sure to Delete')">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash"></i>Delete</button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link mb-2" href="#">Manual Backup History : </a>
            </li>
        </ul>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Router</th>
                    <th scope="col">Status</th>
                    <th scope="col">Time</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($backup_requests as $request )
                <tr>
                    <td scope="row">{{ $request->id }}</td>
                    <td>{{ $request->backup_setting->nas_ip }}</td>
                    <td>{{ $request->status }}</td>
                    <td>{{ $request->created_at }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>

    </div>

</div>


{{-- Notes --}}
<div class="card card-outline card-danger">
    <div class="card-header">
        Notes:
    </div>
    @include('admins.components.authenticator-warnings')
</div>
{{-- Notes --}}

@endsection

@section('pageJs')
@endsection