@extends ('laraview.layouts.sideNavLayout')

@section('title')
Free IPv4pool From Used
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
<h3>Free IPv4pool From Used</h3>
@endsection

@section('content')

<div class="card">

    <form method="POST" action="{{ route('ipv4pool_replace.update', ['ipv4pool' => $ipv4pool->id]) }}">
        @csrf

        @method('put')

        <div class="card-body">

            <!--name-->
            <div class="form-group">

                <label for="name">IPv4pool Name (To be free from used)</label>

                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $ipv4pool->name }}" disabled>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/name-->

            <!--ipv4pool_id-->
            <div class="form-group">

                <label for="ipv4pool_id"><span class="text-danger">*</span>Replaced IPv4 Pool</label>

                <select class="form-control" id="ipv4pool_id" name="ipv4pool_id" required>
                    @foreach ($ipv4pools as $ipv4pool)
                    <option value="{{ $ipv4pool->id }}">
                        {{ $ipv4pool->name }} ({{ long2ip($ipv4pool->subnet).'/'. $ipv4pool->mask }})
                    </option>
                    @endforeach
                </select>

                @error('ipv4pool_id')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/ipv4pool_id-->

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