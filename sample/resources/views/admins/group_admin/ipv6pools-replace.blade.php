@extends ('laraview.layouts.sideNavLayout')

@section('title')
Replace IPv6pool
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
<h3>Replace IPv6pool</h3>
@endsection

@section('content')

<div class="card">

    <p class="text-danger">* required field</p>

    <form method="POST" action="{{ route('ipv6pool_replace.update', ['ipv6pool' =>  $ipv6pool->id ]) }}">

        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">
                <label for="name">IPv6pool Name (To be Replaced)</label>

                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $ipv6pool->name }}" readonly>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/name-->

            <!--ipv6pool_id-->
            <div class="form-group">
                <label for="ipv6pool_id"><span class="text-danger">*</span>IPv6 Pool</label>
                <select class="form-control" id="ipv6pool_id" name="ipv6pool_id" required>
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
