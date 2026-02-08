@extends ('laraview.layouts.sideNavLayout')

@section('title')
Foreign Routers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '5';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection


@section('contentTitle')
<h3>foreign routers</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form id="quickForm" method="GET" action="{{ route('foreign-routers.index') }}">

        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <!--node-->
                    <div class="form-group">
                        <label for="node"><span class="text-danger">*</span>Node</label>
                        <select class="form-control" id="node" name="node" required>
                            <option value="">Please select... </option>
                            @foreach ($nodes as $node)
                            <option value="{{ $node }}">{{ $node }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/node-->
                </div>
                <!--/col-sm--->
            </div>
            <!--/row-->
        </div>
        <!--/card-body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">Submit</button>
        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')
@endsection
