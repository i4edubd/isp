@extends ('laraview.layouts.sideNavLayout')

@section('title')
Complaints
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

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('customer_complains.index') }}" method="get">

    <!--Customer's Complaint-->
    <div class="nav-item">
        <a class="btn btn-primary" href="{{ route('customers.index') }}" role="button">
            <i class="fas fa-plus"></i>
            Customer's Complaint
        </a>
    </div>
    <!--/Customer's Complaint-->

    <!--General Complaint-->
    <div class="nav-item ml-2">
        <a class="btn btn-secondary" href="{{ route('general-customer-complaints.create') }}" role="button">
            <i class="fas fa-plus"></i>
            General Complaint
        </a>
    </div>
    <!--/General Complaint-->

    {{-- department_id --}}
    <div class="form-group col-md-2">
        <select name="department_id" id="department_id" class="form-control">
            <option value=''>department...</option>
            @foreach ($departments as $department)
            <option value="{{ $department->id }}">{{ $department->name }}</option>
            @endforeach
        </select>
    </div>
    {{--department_id --}}

    {{-- category_id --}}
    <div class="form-group col-md-2">
        <select name="category_id" id="category_id" class="form-control">
            <option value=''>category...</option>
            @foreach ($complain_categories as $complain_category)
            <option value="{{ $complain_category->id }}">{{ $complain_category->name }}</option>
            @endforeach
        </select>
    </div>
    {{--category_id --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>

{{-- @endFilter --}}

@endsection

@include('complaint_management.complaint-lists')
