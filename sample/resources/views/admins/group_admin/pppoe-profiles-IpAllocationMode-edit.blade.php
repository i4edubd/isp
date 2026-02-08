@extends ('laraview.layouts.sideNavLayout')

@section('title')
PPP Profile's IPv4Pool Edit
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
<h3>PPP Profile's IPv4 Allocation Mode Edit</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST"
        action="{{ route('pppoe_profile_ip_allocation_mode.update', ['pppoe_profile' => $pppoe_profile]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">

                <label for="name">PPPoE Profile Name</label>

                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $pppoe_profile->name }}" disabled>

            </div>
            <!--/name-->

            <!--ipv4pool_id-->
            <div class="form-group">

                <label for="ipv4pool_id">IPv4 Pool</label>

                <select class="form-control" id="ipv4pool_id" disabled>

                    <option selected>
                        {{ long2ip($pppoe_profile->ipv4pool->subnet) .'/' . $pppoe_profile->ipv4pool->mask }}
                    </option>

                </select>

            </div>
            <!--/ipv4pool_id-->

            <!--ipv6pool_id-->
            <div class="form-group">

                <label for="ipv6pool_id">IPv6 Pool</label>

                <select class="form-control" id="ipv6pool_id" disabled>
                    <option selected>
                        {{ $pppoe_profile->ipv6pool->prefix }}
                    </option>
                </select>

            </div>
            <!--/ipv6pool_id-->

            <!--ip_allocation_mode-->
            <div class="form-group">
                <label for="ip_allocation_mode"><span class="text-danger">*</span>IPv4 Allocation Mode</label>
                <select class="form-control" id="ip_allocation_mode" name="ip_allocation_mode" required>
                    <option value="{{ $pppoe_profile->ip_allocation_mode }}">{{ $pppoe_profile->ip_allocation_mode }}
                    </option>
                    <option value="{{ $new_mode }}">{{ $new_mode }}</option>
                </select>
                @error('ip_allocation_mode')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <!--/ip_allocation_mode-->

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