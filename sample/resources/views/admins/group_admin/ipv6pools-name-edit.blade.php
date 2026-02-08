@extends ('laraview.layouts.sideNavLayout')

@section('title')
IPv6 pool Name Edit
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
<h3>IPv6pool Name Change</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('ipv6pool_name.update', ['ipv6pool' => $ipv6pool->id]) }}">
        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">

                <label for="name"><span class="text-danger">*</span>IPv6pool Name</label>

                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $ipv6pool->name }}" onblur="checkDuplicateName(this.value)" required>

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
                <label for="prefix">Prefix</label>
                <input type="text" class="form-control @error('prefix') is-invalid @enderror" id="prefix"
                    value="{{ $ipv6pool->prefix }}" disabled>

                @error('prefix')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

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
</script>

@endsection
