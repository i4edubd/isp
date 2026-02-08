@extends ('laraview.layouts.topNavLayout')

@section('title')
    Card Recharge
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
                $active_link = '8';
            @endphp
            @include('customers.nav-links')
        </div>
        {{-- Navigation bar --}}

        <div class="row">
            <!-- left column -->
            <div class="col-md-6">
                <div class="card-body">
                    <form id="quickForm" method="POST" action="{{ route('customers.card-recharge.store') }}"
                        onsubmit="return disableDuplicateSubmit()">
                        @csrf
                        <!--card_pin-->
                        <div class="form-group">
                            <label for="card_pin"><span class="text-danger">*</span>
                                {{ getLocaleString($operator->id, 'Card PIN') }}
                            </label>
                            <input name="card_pin" type="text" maxlength="32"
                                class="form-control @error('card_pin') is-invalid @enderror" id="card_pin"
                                value="{{ old('card_pin') }}" required>
                            @error('card_pin')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <!--/card_pin-->
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        @include('customers.footer-nav-links')

    </div>
@endsection

@section('pageJs')
@endsection
