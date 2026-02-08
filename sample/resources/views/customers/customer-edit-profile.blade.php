@extends ('laraview.layouts.topNavLayout')

@section('title')
    Edit Profile
@endsection

@section('pageCss')
@endsection

@section('company')
    {{ $operator->company }}
@endsection

@section('topNavbar')
@endsection

@section('contentTitle')
    @include('customers.logout-nav')
@endsection

@section('content')
    <div class="card">

        {{-- Navigation bar --}}
        <div class="card-header">
            @php
                $active_link = '0';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}

        <div class="card-body">

            <div class="col-sm-6">

                <form action="{{ route('customers.edit-profile.store') }}" method="POST"
                    onsubmit="return disableDuplicateSubmit()">

                    @csrf

                    <!--name-->
                    <div class="form-group">
                        <label for="name"><span class="text-danger">*</span>Name</label>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" value="{{ $customer->name }}" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <!--/name-->

                    <!--zone_id-->
                    <div class="form-group">
                        <label for="zone_id">Customer Zone</label>
                        <select class="form-control" id="zone_id" name="zone_id">
                            @foreach ($customer_zones as $customer_zone)
                                <option value="{{ $customer_zone->id }}">{{ $customer_zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/zone_id-->

                    <button type="submit" id="submit-button" class="btn btn-dark">Submit</button>

                </form>

            </div>

        </div>

        @include('customers.footer-nav-links')

    </div>
@endsection

@section('pageJs')
@endsection
