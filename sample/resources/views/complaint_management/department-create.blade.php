@extends ('laraview.layouts.sideNavLayout')

@section('title')
Departments
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '20';
$active_link = '1';
@endphp
@endsection

@section('sidebar')

@if (Auth::user()->role == 'group_admin')
@include('admins.group_admin.sidebar')
@endif

@if (Auth::user()->role == 'operator')
@include('admins.operator.sidebar')
@endif

@if (Auth::user()->role == 'sub_operator')
@include('admins.sub_operator.sidebar')
@endif

@if (Auth::user()->role == 'manager')
@include('admins.manager.sidebar')
@endif

@endsection

@section('contentTitle')

<h3> New Department </h3>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">


                <form method="POST" action="{{ route('departments.store') }}">

                    @csrf

                    <!--name-->
                    <div class="form-group">
                        <label for="name"><span class="text-danger">*</span>Department Name</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" value="{{ old('name') }}" autocomplete="name" required>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/name-->

                    <button type="submit" class="btn btn-dark">SUBMIT</button>

                </form>

            </div>
            <!--/col-sm-6-->

        </div>
        <!--/row-->

    </div>
    <!--/card body-->

</div>

@endsection

@section('pageJs')
@endsection
