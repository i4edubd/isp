@extends ('laraview.layouts.sideNavLayout')

@section('title')
Edit IPv4pool Name
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
<h3>Edit IPv4pool Name</h3>
@endsection

@section('content')

<div class="card">

    <form method="POST" action="{{ route('ipv4pool_name.update', ['ipv4pool' => $ipv4pool->id]) }}">
        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">
                <label for="name"><span class="text-danger">*</span>IPv4pool Name</label>
                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $ipv4pool->name }}" onblur="checkDuplicateName(this.value)" required>
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
                <label for="subnet">Subnet</label>
                <input type="text" class="form-control @error('subnet') is-invalid @enderror" id="subnet"
                    value="{{ long2ip($ipv4pool->subnet).'/'. $ipv4pool->mask }}" disabled>

                @error('subnet')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

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

</script>

@endsection
