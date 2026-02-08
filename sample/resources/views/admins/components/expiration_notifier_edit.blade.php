@section('contentTitle')
    <h3>Edit Expiration Notification</h3>
@endsection

@section('content')
    <form action="{{ route('expiration_notifiers.update', ['expiration_notifier' => $expiration_notifier]) }}" method="POST">

        @csrf

        @method('PUT')

        <div class="card">

            <div class="card-body">

                <!--status-->
                <div class="form-group">
                    <label for="status"><span class="text-danger">*</span>Status</label>
                    <div class="input-group">
                        <select class="form-control" id="status" name="status" required>
                            <option selected>{{ $expiration_notifier->status }}</option>
                            <option>active</option>
                            <option>inactive</option>
                        </select>
                    </div>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <!--/status-->

                {{-- connection_types --}}
                <p><strong> Connection Types : </strong></p>
                @foreach ($checked_connection_types as $checked_connection_type)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="{{ $checked_connection_type }}"
                            name="{{ $checked_connection_type }}" value="{{ $checked_connection_type }}" checked>
                        <label class="form-check-label"
                            for="{{ $checked_connection_type }}">{{ $checked_connection_type }}</label>
                    </div>
                @endforeach

                @foreach ($unchecked_connection_types as $unchecked_connection_type)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="{{ $unchecked_connection_type }}"
                            name="{{ $unchecked_connection_type }}" value="{{ $unchecked_connection_type }}">
                        <label class="form-check-label"
                            for="{{ $unchecked_connection_type }}">{{ $unchecked_connection_type }}</label>
                    </div>
                @endforeach
                {{-- connection_types --}}

                <hr>

                {{-- billing_types --}}
                <p><strong> Billing Types : </strong></p>
                @foreach ($checked_billing_types as $checked_billing_type)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="{{ $checked_billing_type }}"
                            name="{{ $checked_billing_type }}" value="{{ $checked_billing_type }}" checked>
                        <label class="form-check-label"
                            for="{{ $checked_billing_type }}">{{ $checked_billing_type }}</label>
                    </div>
                @endforeach

                @foreach ($unchecked_billing_types as $unchecked_billing_type)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="{{ $unchecked_billing_type }}"
                            name="{{ $unchecked_billing_type }}" value="{{ $unchecked_billing_type }}">
                        <label class="form-check-label"
                            for="{{ $unchecked_billing_type }}">{{ $unchecked_billing_type }}</label>
                    </div>
                @endforeach
                {{-- billing_types --}}

                <hr>

                <!--notify_before-->
                <div class="form-group">
                    <label for="notify_before"><span class="text-danger">*</span>Notify Before</label>
                    <div class="input-group">
                        <input name="notify_before" type="number"
                            class="form-control @error('notify_before') is-invalid @enderror" id="notify_before"
                            value="{{ $expiration_notifier->notify_before }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text">
                                Day
                            </span>
                        </div>
                    </div>
                    @error('notify_before')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <!--/notify_before-->


                {{-- message --}}
                <div class="form-group">
                    <label for="message"><span class="text-danger">*</span>Message</label>
                    <textarea name="message" class="form-control" id="message" rows="3" required>{{ $message }}</textarea>
                </div>
                {{-- message --}}

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark">Submit</button>
            </div>

        </div>

    </form>
@endsection

@section('pageJs')
@endsection
