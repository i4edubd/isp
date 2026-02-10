@extends('laraview.layouts.sideNavLayout')

@section('title')
Import PPPoE Customers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3> Import PPPoE Customers </h3>
@endsection

@section('content')

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('pppoe-import-store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Operator -->
            <div class="form-group">
                <label for="operator_id"><span class="text-danger">*</span>Operator</label>
                <select name="operator_id" id="operator_id" class="form-control select2" required>
                    @foreach ($operators as $operator )
                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Format -->
            <div class="form-group">
                <label for="date_format"><span class="text-danger">*</span>Date Format</label>
                <select name="date_format" id="date_format" class="form-control select2" required>
                    <option value="Y-m-d">Y-m-d</option>
                    <option value="d-m-Y">d-m-Y</option>
                </select>
            </div>

            <!-- Excel File -->
            <div class="form-group">
                <label for="file"><span class="text-danger">*</span>Excel File</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-dark">Import</button>
        </form>
    </div>
</div>

@endsection