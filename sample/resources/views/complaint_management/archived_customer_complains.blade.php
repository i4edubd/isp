@extends ('laraview.layouts.sideNavLayout')

@section('title')
Archived Complaints
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '20';
$active_link = '4';
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
<form class="d-flex align-content-start flex-wrap" action="{{ route('archived_customer_complains.index') }}"
    method="get">

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

    {{-- year --}}
    <div class="form-group col-md-2">
        <select name="year" id="year" class="form-control">
            <option value=''>year...</option>
            @php
            $start = date(config('app.year_format'));
            $stop = $start - 5;
            @endphp
            @for($i = $start; $i >= $stop; $i--)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>
    </div>
    {{--year --}}

    {{-- month --}}
    <div class="form-group col-md-2">
        <select name="month" id="month" class="form-control">
            <option value=''>month...</option>
            <option value='January'>January</option>
            <option value='February'>February</option>
            <option value='March'>March</option>
            <option value='April'>April</option>
            <option value='May'>May</option>
            <option value='June'>June</option>
            <option value='July'>July</option>
            <option value='August'>August</option>
            <option value='September'>September</option>
            <option value='October'>October</option>
            <option value='November'>November</option>
            <option value='December'>December</option>
        </select>
    </div>
    {{--month --}}

    {{-- Page length --}}
    <div class="form-group col-md-2">
        <select name="length" id="length" class="form-control">
            <option value="{{ $length }}" selected>Show {{ $length }} entries </option>
            <option value="10">Show 10 entries</option>
            <option value="25">Show 25 entries</option>
            <option value="50">Show 50 entries</option>
            <option value="100">Show 100 entries</option>
        </select>
    </div>
    {{--Page length --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>
{{-- @endFilter --}}

@endsection

@include('complaint_management.complaint-lists')
