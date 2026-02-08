@extends ('laraview.layouts.sideNavLayout')

@section('title')
Reporting
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '20';
$active_link = '5';
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
<h3> Reporting </h3>
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap mt-2" action="{{ route('complaint-reporting.index') }}" method="get">

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

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>

{{-- @endFilter --}}

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">Year</th>
                    <th scope="col">Month</th>
                    <th scope="col">Category</th>
                    <th scope="col">Complaints Count</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                <tr>
                    <td>{{ $report['year'] }}</td>
                    <td>{{ $report['month'] }}</td>
                    <td>{{ $report['category'] }}</td>
                    <td>{{ $report['total_count'] }}</td>
                    <td>
                        <a
                            href="{{ route('archived_customer_complains.index', ['category_id' => $report['category_id'], 'year' => $report['year'], 'month' => $report['month']]) }}">
                            <i class="fas fa-expand-alt"></i>
                            Details
                        </a>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    <!--/card body-->

</div>

@endsection

@section('pageJs')
@endsection
