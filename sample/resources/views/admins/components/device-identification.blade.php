@section('contentTitle')
    <h3>Device Identification</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-header font-weight-bold">
            What is device identification service?
        </div>

        <div class="card-body">
            <p>When you enable the device identification service, a one-time password will be sent to your mobile number
                when you sign in to the software from a new device. You have to pay for SMS.</p>
        </div>

        <div class="card-footer">
            @if ($operator->device_identification_enabled)
                The device identification service is active.
                <form action="{{ route('disable-device-identification') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-dark mt-4">DISABLE</button>
                </form>
            @else
                <a class="btn btn-outline-dark"
                    href="{{ route('operators.device-identification.create', ['operator' => $operator]) }}" role="button"
                    onclick="return confirm('An SMS will be sent to your phone ({{ $operator->mobile }})')">
                    Enable device identification service
                </a>
            @endif
        </div>

    </div>
@endsection

@section('pageJs')
@endsection
