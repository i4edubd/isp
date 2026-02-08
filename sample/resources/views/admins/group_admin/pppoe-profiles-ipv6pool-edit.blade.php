@extends ('laraview.layouts.sideNavLayout')

@section('title')
PPPoE Profile IPv6Pool Edit
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
<h3>PPPoE Profile IPv6Pool Edit</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('pppoe_profile_ipv6pool.update', ['pppoe_profile' => $pppoe_profile->id]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">

                <label for="name">PPPoE Profile Name</label>

                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $pppoe_profile->name }}" disabled>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <div id="duplicate_name_response"></div>

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

                @error('ipv4pool_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/ipv4pool_id-->

            <!--ipv6pool_id-->
            <div class="form-group">
                <label for="ipv6pool_id"><span class="text-danger">*</span>IPv6 Pool</label>
                <select class="form-control" id="ipv6pool_id" name="ipv6pool_id" required>
                    <option value="{{ $pppoe_profile->ipv6pool_id }}" selected>{{ $pppoe_profile->ipv6pool->prefix }}
                    </option>
                    @foreach ($ipv6pools as $ipv6pool)
                    <option value="{{ $ipv6pool->id }}">{{ $ipv6pool->name }} ({{ $ipv6pool->prefix }})</option>
                    @endforeach
                </select>
                @error('ipv6pool_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <!--/ipv6pool_id-->

            <!--ip_allocation_mode-->
            <div class="form-group">

                <label for="ip_allocation_mode">IPv4 IP Allocation</label>

                <select class="form-control" id="ip_allocation_mode" disabled>
                    <option>{{ $pppoe_profile->ip_allocation_mode}}</option>
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
