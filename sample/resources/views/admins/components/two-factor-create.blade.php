@section('contentTitle')
    <h3>Two Factor Authentication</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <p class="card-text">
                You are enabling two factor authentication.
            </p>

            <p class="card-text">
                When two factor authentication is enabled, you will be prompted for a secure, random token during
                authentication. You may retrieve this token from your phone's
                <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en&gl=US">
                    Google Authenticator </a> application.
            </p>

            <p class="card-text">
                To Enable Two factor authentication, Scan the following QR code using your phone's authenticator
                application.
            </p>

            <div class="row">

                <div class="col-6">

                    <img src="data:image/svg+xml;base64, {{ $qrcode_image }}" />

                    <form action="{{ route('two-factor.store') }}" method="post">

                        @csrf

                        <!--code-->
                        <div class="input-group mb-3">

                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                placeholder="Code" required autocomplete="code" autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-mobile-alt"></span>
                                </div>
                            </div>

                            @error('code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                        </div>
                        <!--code-->

                        <!--submit-->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-dark">SUBMIT</button>
                            </div>
                        </div>
                        <!--/submit-->

                    </form>

                </div>

            </div>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
