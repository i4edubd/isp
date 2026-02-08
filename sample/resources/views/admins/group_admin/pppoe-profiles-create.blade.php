@extends ('laraview.layouts.sideNavLayout')

@section('title')
New PPPoE Profile
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
<h3>New PPPoE Profile</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('pppoe_profiles.store') }}">
        @csrf

        <div class="card-body">

            <!--name-->
            <div class="form-group">
                <label for="name"><span class="text-danger">*</span>PPPoE Profile Name</label>
                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ old('name') }}" onblur="checkDuplicateName(this.value)" required>
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
                <label for="ipv4pool_id"><span class="text-danger">*</span>IPv4 Pool</label>
                <select class="form-control" id="ipv4pool_id" name="ipv4pool_id" required>
                    @foreach ($ipv4pools as $ipv4pool)
                    <option value="{{ $ipv4pool->id }}">{{ $ipv4pool->name }}
                        ({{ long2ip($ipv4pool->subnet).'/'. $ipv4pool->mask }})</option>
                    @endforeach
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
                <label for="ipv6pool_id">IPv6 Pool</label>
                <select class="form-control" id="ipv6pool_id" name="ipv6pool_id">
                    <option value="">Please select...</option>
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
                <label for="ip_allocation_mode"><span class="text-danger">*</span>IPv4 Allocation Mode</label>
                <select class="form-control" id="ip_allocation_mode" name="ip_allocation_mode" required>
                    <option value="static">static</option>
                    <option value="dynamic">dynamic</option>
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

<script>
    function checkDuplicateName(name)
    {
        $.get( '/admin/variable-name?string=' + name, function( data ) {
            $("#name").val(data);
        });

        let url = "/admin/pppoe_profiles/check/duplicate/name/" + name;
        $.get( url, function( data ) {
            $("#duplicate_name_response").html(data);
        });

    }

</script>

@endsection