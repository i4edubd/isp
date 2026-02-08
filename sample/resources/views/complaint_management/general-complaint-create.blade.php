@extends ('laraview.layouts.sideNavLayout')

@section('title')
New Complain
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '20';
$active_link = '3';
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

<h3>New Complain</h3>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">


                <form method="POST" action="{{ route('general-customer-complaints.store') }}">

                    @csrf

                    <!--category_id-->
                    <div class="form-group">
                        <label for="category_id"><span class="text-danger">*</span>Category</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value=''>category...</option>
                            @foreach ($complain_categories as $complain_category)
                            <option value="{{ $complain_category->id }}">{{ $complain_category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/category_id-->

                    <!--department_id-->
                    <div class="form-group">
                        <label for="department_id"><span class="text-danger">*</span>Department</label>
                        <select class="form-control" id="department_id" name="department_id" required>
                            <option value=''>department...</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/department_id-->

                    {{-- message --}}
                    <div class="form-group">
                        <label for="message">Complain</label>
                        <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                    </div>
                    {{-- message --}}

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
