@section('contentTitle')
    <h3>Expiration Notification</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <ul class="list-group">

                <li class="list-group-item"><span class="font-weight-bold">Status: </span>
                    {{ $expiration_notifier->status }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Connection Types: </span>
                    @foreach (json_decode($expiration_notifier->connection_types, true) as $connection_type)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="{{ $connection_type }}" checked>
                            <label class="form-check-label" for="{{ $connection_type }}">{{ $connection_type }}</label>
                        </div>
                    @endforeach
                </li>

                <li class="list-group-item"><span class="font-weight-bold">Billing Types: </span>
                    @foreach (json_decode($expiration_notifier->billing_types, true) as $billing_type)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="{{ $billing_type }}" checked>
                            <label class="form-check-label" for="{{ $billing_type }}">{{ $billing_type }}</label>
                        </div>
                    @endforeach
                </li>

                <li class="list-group-item"><span class="font-weight-bold"> Notify Before: </span>
                    {{ $expiration_notifier->notify_before }} {{ $expiration_notifier->unit }}
                </li>

                <li class="list-group-item"><span class="font-weight-bold"> Message: </span>
                    {{ $message }}
                </li>

            </ul>

        </div>

        <div class="card-footer">

            <a href="{{ route('expiration_notifiers.edit', ['expiration_notifier' => $expiration_notifier]) }}"
                class="card-link">
                EDIT
            </a>

        </div>

    </div>
@endsection

@section('pageJs')
@endsection
