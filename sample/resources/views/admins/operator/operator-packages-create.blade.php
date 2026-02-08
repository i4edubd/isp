@extends ('laraview.layouts.sideNavLayout')

@section('title')
Assign Package
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '10';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.operator.sidebar')
@endsection

@section('contentTitle')
<h3> Assign Package </h3>
@endsection


@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title font-weight-bold">Reseller: {{ $operator->name }}</h3>
    </div>

</div>

<form id="quickForm" method="POST" action="{{ route('operators.packages.store', ['operator' => $operator->id]) }}">

    @csrf

    {{-- PPP Packages --}}
    <div class="card card-outline card-primary">

        <div class="card-header">
            <h3 class="card-title">PPP Packages</h3>
        </div>

        <div class="card-body">

            {{-- Select Package --}}
            @foreach ($packages->filter(function ($value, $key) {
            return $value->master_package->connection_type == 'PPPoE';
            })->sortBy('name') as $package)
            <div class="form-check">
                <input name="package_id" class="form-check-input" type="radio" value="{{ $package->id }}"
                    id="{{ $package->id }}">
                <label class="form-check-label" for="{{ $package->id }}">
                    {{ $package->name }}
                </label>
            </div>
            @endforeach
            {{-- Select Package --}}

        </div>

    </div>
    {{-- PPP Packages --}}

    {{-- Hotspot Packages --}}
    <div class="card card-outline card-secondary">

        <div class="card-header">
            <h3 class="card-title">Hotspot Packages</h3>
        </div>

        <div class="card-body">

            {{-- Select Package --}}
            @foreach ($packages->filter(function ($value, $key) {
            return $value->master_package->connection_type == 'Hotspot';
            })->sortBy('name') as $package)
            <div class="form-check">
                <input name="package_id" class="form-check-input" type="radio" value="{{ $package->id }}"
                    id="{{ $package->id }}">
                <label class="form-check-label" for="{{ $package->id }}">
                    {{ $package->name }}
                </label>
            </div>
            @endforeach
            {{-- Select Package --}}

        </div>

    </div>
    {{-- Hotspot Packages --}}

    {{-- Other Packages --}}
    <div class="card card-outline card-secondary">

        <div class="card-header">
            <h3 class="card-title">Other Packages</h3>
        </div>

        <div class="card-body">

            {{-- Select Package --}}
            @foreach ($packages->filter(function ($value, $key) {
            return $value->master_package->connection_type == 'Other';
            })->sortBy('name') as $package)
            <div class="form-check">
                <input name="package_id" class="form-check-input" type="radio" value="{{ $package->id }}"
                    id="{{ $package->id }}">
                <label class="form-check-label" for="{{ $package->id }}">
                    {{ $package->name }}
                </label>
            </div>
            @endforeach
            {{-- Select Package --}}

        </div>

    </div>
    {{-- Other Packages --}}

    <div class="card">
        <div class="card-footer">
            <button type="submit" class="btn btn-primary mt-2">Next</button>
        </div>
    </div>

</form>

@endsection

@section('pageJs')
@endsection