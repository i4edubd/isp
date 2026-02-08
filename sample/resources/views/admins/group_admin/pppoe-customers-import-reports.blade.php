@extends ('laraview.layouts.sideNavLayout')

@section('title')
Customer Import Reports
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
<h3>Customer Import Reports</h3>
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap"
    action="{{ route('pppoe_customers_import.show', ['pppoe_customers_import' => $pppoe_customers_import->id]) }}"
    method="get">

    {{-- status --}}
    <div class="form-group col-md-2">
        <select name="status" id="status" class="form-control">
            <option value=''>status...</option>
            <option value='success'>success</option>
            <option value='failed'>failed</option>
        </select>
    </div>
    {{--status --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>
{{-- @endFilter --}}

<div class="card">

    <div class="card-body">

        {{-- IPv4 Pool --}}
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="#">IPv4 Pool</a>
            </li>
        </ul>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Comment</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($reports->where('menu', 'ipv4pool') as $report )
                <tr>
                    <td scope="row">{{ $report->id }}</td>
                    <td>{{ $report->name }}</td>
                    <td>{{ $report->status }}</td>
                    <td>{{ $report->comment }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>
        {{-- IPv4 Pool --}}

        {{-- pppoe_profile --}}
        <ul class="nav nav-pills mt-4">
            <li class="nav-item">
                <a class="nav-link active" href="#">PPP profile</a>
            </li>
        </ul>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Comment</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($reports->where('menu', 'pppoe_profile') as $report )
                <tr>
                    <td scope="row">{{ $report->id }}</td>
                    <td>{{ $report->name }}</td>
                    <td>{{ $report->status }}</td>
                    <td>{{ $report->comment }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>

        {{-- pppoe_profile --}}


        {{-- customer --}}
        <ul class="nav nav-pills mt-4">
            <li class="nav-item">
                <a class="nav-link active" href="#">customer</a>
            </li>
        </ul>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Status</th>
                    <th scope="col">Comment</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($reports->where('menu', 'customer') as $report )
                <tr>
                    <td scope="row">{{ $report->id }}</td>
                    <td>{{ $report->name }}</td>
                    <td>{{ $report->status }}</td>
                    <td>{{ $report->comment }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>

        {{-- customer --}}

    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')
@endsection
