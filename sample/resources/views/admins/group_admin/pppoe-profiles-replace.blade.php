@extends ('laraview.layouts.sideNavLayout')

@section('title')
PPPoE Profile Replace
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '4';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>PPP Profile Replace</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('pppoe_profile_replace.update', ['pppoe_profile' => $pppoe_profile->id]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">

                <label for="name">PPP Profile Name (To be Replaced)</label>

                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $pppoe_profile->name }}" disabled>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/name-->

            <!--pppoe_profile_id-->
            <div class="form-group">
                <label for="pppoe_profile_id"><span class="text-danger">*</span>Select PPP Profile</label>
                <select class="form-control" id="pppoe_profile_id" name="pppoe_profile_id" required>
                    <option value="" selected>please select...</option>
                    @foreach ($profiles as $profile)
                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                    @endforeach
                </select>
                @error('pppoe_profile_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <!--/pppoe_profile_id-->

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