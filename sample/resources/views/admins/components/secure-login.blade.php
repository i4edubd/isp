@section('contentTitle')
<h3>Secure Login</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        @if ($operator->webauthn_enabled)
        <p class="card-text">
            The secure login is activated.
        </p>
        @else
        <form id="register-form">
            <button type="submit" class="btn btn-outline-dark">Register Secure Login</button>
        </form>
        @endif

    </div>

</div>

@endsection

@section('pageJs')
@endsection