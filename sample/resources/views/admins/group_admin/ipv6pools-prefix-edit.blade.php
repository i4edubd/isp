@extends ('laraview.layouts.sideNavLayout')

@section('title')
IPv6pool Prefix Edit
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '3';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3>IPv6pool Prefix Edit</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('ipv6pool_subnet.update', ['ipv6pool' =>  $ipv6pool->id ]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">
                <label for="name">IPv6pool Name</label>

                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $ipv6pool->name }}" readonly>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/name-->

            <!--prefix-->
            <div class="form-group">

                <label for="prefix"><span class="text-danger">*</span>Prefix</label>

                <input name="prefix" type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix"
                    value="{{ $ipv6pool->prefix }}" onblur="checkPrefixError(this.value)" required>

                @error('prefix')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

                <div id="prefix_error_check_response"></div>

            </div>
            <!--/prefix-->

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
    function checkPrefixError(prefix)
    {
        let url = "/admin/ipv6pool_subnet/check/{{ $ipv6pool->id }}/error?prefix=" + prefix;
        $.get( url, function( data ) {
            $("#prefix_error_check_response").html(data);
        });
    }

</script>

@endsection
