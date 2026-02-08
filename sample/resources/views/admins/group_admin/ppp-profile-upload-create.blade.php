@extends ('laraview.layouts.sideNavLayout')

@section('title')
New customer
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '1';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Select Router </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">


                <form method="POST" action="{{ route('upload-ppp-profile') }}">

                    @csrf


                    <!--router_id-->
                    <div class="form-group">
                        <label for="router_id"><span class="text-danger">*</span>Router</label>

                        <select class="form-control" id="router_id" name="router_id" required>

                            @foreach ($routers as $router)
                            <option value="{{ $router->id }}">{{ $router->location }} :: {{ $router->nasname }}</option>
                            @endforeach

                        </select>

                        @error('router_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <!--/router_id-->


                    <button type="submit" class="btn btn-dark">NEXT<i class="fas fa-arrow-right"></i></button>

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
