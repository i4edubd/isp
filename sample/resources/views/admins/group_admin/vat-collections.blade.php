@extends ('laraview.layouts.sideNavLayout')

@section('title')
VAT Collection
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '40';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3> VAT Collections </h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">VAT</li>
    <li class="breadcrumb-item active">Collections</li>
</ol>
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('vat_collections.index') }}" method="get">

    {{-- year --}}
    <div class="form-group col-md-2">
        <select name="year" id="year" class="form-control">
            <option value=''>Year...</option>
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

        <table id="data_table" class="table table-bordered">

            <thead>

                <tr>
                    <th scope="col">Year</th>
                    <th scope="col">Month</th>
                    <th scope="col">VAT Profile</th>
                    <th scope="col">Amount</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($collections as $month => $month_group)

                @foreach ($month_group->groupBy('vat_profile_id') as $vat_profile_id => $vat_collections)

                <tr>
                    <td scope="row">{{ $year}}</td>
                    <td>{{ $month }}</td>
                    <td>{{ getVatProfileDescription($vat_profile_id) }}</td>
                    <td>{{ $vat_collections->sum('amount') }}</td>
                </tr>

                @endforeach

                @endforeach

            </tbody>

        </table>

    </div>
    <!-- /card-body -->

</div>

@endsection

@section('pageJs')
@endsection