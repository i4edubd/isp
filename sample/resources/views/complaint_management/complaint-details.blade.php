@extends ('laraview.layouts.sideNavLayout')

@section('title')
Complaint Details
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

<h3>Complaint Details</h3>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        {{-- action row --}}
        @if ($customer_complain->is_active == 1)

        <div class="row mb-4">

            <!--transfer-->
            <div class="col-sm-3">
                <form class="form-inline"
                    action="{{ route('customer_complains.departments.store', ['customer_complain' => $customer_complain->id]) }}"
                    method="post">
                    @csrf
                    <!--department_id-->
                    <div class="form-group sm-4">
                        <label for="department_id" class="sr-only">Department</label>
                        <select name="department_id" id="department_id" class="form-control" required>
                            <option value=''>department...</option>

                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <!--/department_id-->
                    <button type="submit" name="transfer" class="btn btn-primary sm-2">Transfer</button>
                </form>
            </div>
            <!--/transfer-->


            <!--category Change-->
            <div class="col-sm-3">
                <form class="form-inline"
                    action="{{ route('customer_complains.complain_categories.store', ['customer_complain' => $customer_complain->id ]) }}"
                    method="post">
                    @csrf
                    <!--category_id-->
                    <div class="form-group sm-4">
                        <label for="category_id" class="sr-only">Category</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value=''>category...</option>

                            @foreach ($complain_categories as $complain_category)
                            <option value="{{ $complain_category->id }}">{{ $complain_category->name }}</option>
                            @endforeach

                        </select>
                    </div>
                    <!--/category_id-->
                    <button type="submit" name="transfer" class="btn btn-primary sm-2">Change</button>
                </form>
            </div>
            <!--/category Change-->


            <!--destroy-->
            <div class="col-sm-3">
                <form class="form-inline"
                    action="{{ route('customer_complains.destroy', ['customer_complain' => $customer_complain->id]) }}"
                    method="post" onsubmit="return confirm('Are you sure to Delete')">
                    @csrf
                    @method('delete')

                    <button type="submit" name="transfer" class="btn btn-danger sm-2">DELETE</button>
                </form>
            </div>
            <!--/destroy-->

        </div>

        @endif
        {{-- action row --}}

        {{-- Complaint Information --}}
        <div class="row border mb-4">

            <!--Department-->
            <div class="col-sm-3">
                <span class="font-weight-bold"> Department: </span> {{ $customer_complain->department->name }}
            </div>
            <!--/Department-->


            <!--Category-->
            <div class="col-sm-3">
                <span class="font-weight-bold"> Category: </span> {{ $customer_complain->category->name }}
            </div>
            <!--/Category-->

        </div>
        {{-- Complaint Information --}}


        {{-- Customer Information --}}
        <div class="row border mb-4">

            <!--Name-->
            <div class="col-sm-3">
                <span class="font-weight-bold"> Customer Name: </span> {{ $customer->name }}
            </div>
            <!--/Name-->


            <!--Mobile-->
            <div class="col-sm-3">
                <span class="font-weight-bold"> Mobile: </span> {{ $customer->mobile }}
            </div>
            <!--/Mobile-->


            <!--Username-->
            <div class="col-sm-3">
                <span class="font-weight-bold"> Username: </span> {{ $customer->username }}
            </div>
            <!--/Username-->

            <!--Send SMS-->
            @if ($customer_complain->customer_id > 0)
            <div class="col-sm-3">
                <i class="fas fa-paper-plane"></i>
                <a
                    href="{{ route('customers.sms_histories.create', ['customer' => $customer_complain->customer_id]) }}">
                    Send SMS
                </a>
            </div>
            @endif
            <!--/Send SMS-->

        </div>
        {{-- Customer Information --}}

        {{-- timeline --}}

        @include('complaint_management.complaint-timeline')

        {{-- timeline --}}

    </div>
    <!--/card body-->

</div>

@endsection

@section('pageJs')
@endsection
