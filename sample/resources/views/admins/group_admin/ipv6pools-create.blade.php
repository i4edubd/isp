@extends ('laraview.layouts.sideNavLayout')

@section('title')
New IPv6pool
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
<h3>New IPv6pool</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('ipv6pools.store') }}">
        @csrf

        <div class="card-body">

            <!--name-->
            <div class="form-group">
                <label for="name"><span class="text-danger">*</span>IPv6pool Name</label>
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

            <!--prefix-->
            <div class="form-group">
                <label for="prefix"><span class="text-danger">*</span>prefix (Example: face:b00c:4000::/48)</label>
                <input name="prefix" type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix"
                    value="{{ old('prefix') }}" onblur="checkPrefixError(this.value)" required>
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
    function checkDuplicateName(name)
{
    $.get( '/admin/variable-name?string=' + name, function( data ) {
        $("#name").val(data);
    });

    let url = "/admin/ipv6pools/check/duplicate/name/" + name;
    $.get( url, function( data ) {
        $("#duplicate_name_response").html(data);
    });

}

function checkPrefixError(prefix)
{
    let url = "/admin/ipv6pools/check/duplicate/prefix?prefix=" + prefix;
    $.get( url, function( data ) {
        $("#prefix_error_check_response").html(data);
    });
}

</script>

@endsection
