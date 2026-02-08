@extends ('laraview.layouts.sideNavLayout')

@section('title')
New Backup Setting
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
<h3>Edit Backup & Auth Setting</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST"
            action="{{ route('backup_settings.update', ['backup_setting' => $backup_setting->id]) }}">

            @csrf

            @method('PUT')

            <div class="col-sm-6">

                <!--operator_id-->
                <div class="form-group">
                    <label for="operator_id"><span class="text-danger">*</span>Operator</label>
                    <select id="operator_id" name="operator_id" class="form-control" required>
                        <option value="{{ $original_operator->id }}" selected>
                            {{ $original_operator->id }} :: {{ $original_operator->name }} :: {{
                            $original_operator->readable_role }}
                        </option>
                        @foreach ($operators as $operator)
                        <option value="{{ $operator->id }}">
                            {{ $operator->id }} :: {{ $operator->name }} :: {{ $operator->readable_role }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <!--/operator_id-->

                <!--nas_id-->
                <div class="form-group">
                    <label for="nas_id"><span class="text-danger">*</span>Router</label>
                    <select id="nas_id" name="nas_id" class="form-control" required>
                        <option value="{{ $original_router->id }}" selected>
                            {{ $original_router->location }} :: {{ $original_router->nasname }}
                        </option>
                        @foreach ($routers as $router)
                        <option value="{{ $router->id }}">{{ $router->location }} :: {{ $router->nasname }}</option>
                        @endforeach
                    </select>
                </div>
                <!--/nas_id-->

                <!--primary_authenticator-->
                <div class="form-group">
                    <label for="primary_authenticator"><span class="text-danger">*</span>Primary Authenticator</label>
                    <select id="primary_authenticator" name="primary_authenticator" class="form-control" required>
                        <option value="{{ $backup_setting->primary_authenticator }}">{{
                            $backup_setting->primary_authenticator }}</option>
                        <option value="Radius">Radius</option>
                        <option value="Router">Router</option>
                    </select>
                </div>
                <!--/primary_authenticator-->

                <!--backup_type-->
                <div class="form-group">
                    <label for="backup_type"><span class="text-danger">*</span>Backup Type</label>
                    <select id="backup_type" name="backup_type" class="form-control" required>
                        <option value="{{ $backup_setting->backup_type }}">{{ $backup_setting->backup_type }}</option>
                        <option value="automatic">automatic</option>
                        <option value="manual">manual</option>
                    </select>
                </div>
                <!--/backup_type-->

            </div>
            <!--/col-sm-6-->

            <div class="col-sm-6">
                <button type="submit" class="btn btn-dark">Submit</button>
            </div>
        </form>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
