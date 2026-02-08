@extends ('laraview.layouts.sideNavLayout')

@section('title')
IPv4pool
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
<h3>Edit IPv4pool Subnet</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('ipv4pool_subnet.update', ['ipv4pool' => $ipv4pool->id ]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">
                <label for="name"><span class="text-danger">*</span>IPv4pool Name</label>

                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $ipv4pool->name }}" disabled>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/name-->

            <!--subnet-->
            <div class="form-group">
                <label for="subnet"><span class="text-danger">*</span>Subnet (Example: 192.168.1.0/24)</label>
                <input name="subnet" type="text" class="form-control @error('subnet') is-invalid @enderror" id="subnet"
                    value="{{ long2ip($ipv4pool->subnet).'/'. $ipv4pool->mask }}" onblur="checkSubnetError(this.value)"
                    required>

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
    function checkSubnetError(subnet)
    {
        let url = "/admin/ipv4pool_subnet/check/{{ $ipv4pool->id }}/error?subnet=" + subnet;
        $.get( url, function( data ) {
            $("#subnet_error_check_response").html(data);
        });
    }

</script>

@endsection