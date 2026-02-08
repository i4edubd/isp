@extends ('laraview.layouts.sideNavLayout')

@section('title')
New IPv4pool
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '2';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection


@section('contentTitle')
<h3>New IPv4pool</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('ipv4pools.store') }}">
        @csrf

        <div class="card-body">

            <!--name-->
            <div class="form-group">
                <label for="name"><span class="text-danger">*</span>IPv4pool Name</label>
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

            <!--subnet-->
            <div class="form-group">
                <label for="subnet"><span class="text-danger">*</span>Subnet (Example: 192.168.1.0/24)</label>
                <input name="subnet" type="text" class="form-control @error('subnet') is-invalid @enderror" id="subnet"
                    value="{{ old('subnet') }}" onblur="checkSubnetError(this.value)" required>
                @error('subnet')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
                <div id="subnet_error_check_response"></div>
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

<script>
    function checkDuplicateName(name)
{
    $.get( '/admin/variable-name?string=' + name, function( data ) {
        $("#name").val(data);
    });

    let url = "/admin/ipv4pools/check/duplicate/name/" + name;
    $.get( url, function( data ) {
        $("#duplicate_name_response").html(data);
    });

}

function checkSubnetError(subnet)
{
    let url = "/admin/ipv4pools/check/duplicate/subnet?subnet=" + subnet;
    $.get( url, function( data ) {
        $("#subnet_error_check_response").html(data);
    });
}

</script>

@endsection
