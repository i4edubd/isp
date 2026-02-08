@extends ('laraview.layouts.sideNavLayout')

@section('title')
BTRC Report Create
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '8';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')

<h3> BTRC Report Create </h3>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('btrc-report.store') }}">

                    @csrf

                    <!--operator_id-->
                    <div class="form-group">
                        <label for="operator_id"><span class="text-danger">*</span>Operator</label>
                        <select name="operator_id" id="operator_id" class="form-control select2" required>
                            @foreach ($operators as $operator )
                            <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/operator_id-->

                    <button type="submit" class="btn btn-dark">SUBMIT</button>

                </form>

            </div>
            <!--/col-sm-6-->

        </div>
        <!--/row-->

    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')
@endsection
