@extends ('laraview.layouts.sideNavLayoutForCardDistributors')

@section('title')
    change-password
@endsection

@section('activeLink')
    @php
        $active_menu = '4';
        $active_link = '1';
    @endphp
@endsection

@section('sidebar')
    @include('admins.card_distributors.sidebar')
@endsection

@section('contentTitle')
    <h3> Change Password </h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">


            <p class="text-danger">* required field</p>

            <form id="quickForm" method="POST" action="{{ route('card.change-password.store') }}">
                @csrf

                <div class="col-sm-6">

                    <!--password-->
                    <div class="form-group">
                        <label for="password"><span class="text-danger">*</span>Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                            name="password" required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <!--/password-->


                    <!--password-confirm-->
                    <div class="form-group">
                        <label for="password-confirm"><span class="text-danger">*</span>Confirm Password</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            required autocomplete="new-password">

                        @error('password_confirmation')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                    </div>
                    <!--/password-confirm-->

                    <button type="submit" class="btn btn-dark">Change Password</button>

                </div>
                <!--/col-sm-6-->
            </form>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
