@extends ('laraview.layouts.sideNavLayout')

@section('title')
New VPN pool
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '12';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection


@section('contentTitle')
<h3>New VPN pool</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('vpn-pools.store') }}">

        @csrf

        <div class="card-body">

            <!--type-->
            <div class="form-group">
                <label for="type"><span class="text-danger">*</span>Type</label>

                <div class="input-group">

                    <select class="form-control" id="type" name="type" required>
                        <option value="client">client</option>
                        <option value="server">server</option>
                    </select>

                </div>

                @error('type')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/type-->

            <!--subnet-->
            <div class="form-group">

                <label for="subnet"><span class="text-danger">*</span>Subnet (Example: 192.168.1.0/24)</label>

                <input name="subnet" type="text" class="form-control @error('subnet') is-invalid @enderror" id="subnet"
                    value="{{ old('subnet') }}" required>

                @error('subnet')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/subnet-->

        </div>
        <!--/Card Body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">Submit</button>
        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')
@endsection
