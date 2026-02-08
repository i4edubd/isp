@extends ('laraview.layouts.sideNavLayout')

@section('title')
Subscription Payment Report
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.super_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Download subscription payments report</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('subscription-payment-report.store') }}">

                    @csrf

                    {{-- year --}}
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select name="year" id="year" class="form-control" required>
                            <option value=''>Please select...</option>
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
                    <div class="form-group">
                        <label for="month">Month</label>
                        <select name="month" id="month" class="form-control" required>
                            <option value=''>Please select...</option>
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

                    <button type="submit" class="btn btn-danger btn-sm">SUBMIT</button>

                </form>

            </div>
            <!--/col-sm-6-->

        </div>
        <!--/row-->

    </div>

</div>

@endsection

@section('pageJs')
@endsection
