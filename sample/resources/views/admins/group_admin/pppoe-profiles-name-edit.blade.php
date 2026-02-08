@extends ('laraview.layouts.sideNavLayout')

@section('title')
PPP Profile Name Edit
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
<h3>PPP Profile Name Edit</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('pppoe_profile_name.update', ['pppoe_profile' => $pppoe_profile->id]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">

                <label for="name"><span class="text-danger">*</span>PPP Profile Name</label>

                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $pppoe_profile->name }}" onblur="checkDuplicateName(this.value)" required>

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

                <label for="ipv6pool_id">IPv6 Pool</label>

                <select class="form-control" id="ipv6pool_id" disabled>
                    <option selected>
                        {{ $pppoe_profile->ipv6pool->prefix }}
                    </option>
                </select>

                @error('ipv6pool_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/ipv6pool_id-->

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
