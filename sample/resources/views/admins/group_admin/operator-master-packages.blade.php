@extends ('laraview.layouts.sideNavLayout')

@section('title')
Operator Packages
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')

<div class="d-flex align-content-start flex-wrap">

    <form action="{{ route('operators.master_packages.create', ['operator' => $operator->id]) }}">

        <ul class="nav pr-2">
            <li class="nav-item">
                <button type="submit" class="btn btn-primary ml-2"> <i class="fas fa-plus"></i> Add Package</button>
            </li>
        </ul>

    </form>

    <form action="{{ route('operators.master_packages.index', ['operator' => Auth::user()]) }}" method="GET">
        <ul class="nav">
            <!--operator_id-->
            <li class="nav-item">
                <select name="operator_id" id="operator_id" class="form-control">
                    <option value=''>operator...</option>
                    @foreach (Auth::user()->operators->where('role', '!=', 'manager') as $reseller)
                    <option value="{{ $reseller->id }}">
                        {{ $reseller->id }} :: {{ $reseller->name }} :: {{ $reseller->role }}
                    </option>
                    @endforeach
                </select>
            </li>
            <!--/operator_id-->
            <li class="nav-item">
                <button type="submit" class="btn btn-dark ml-2">FILTER</button>
            </li>
        </ul>
    </form>

</div>

@endsection

@include('admins.components.operator-packages')