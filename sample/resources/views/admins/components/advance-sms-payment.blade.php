@section('contentTitle')
<h3>Advance SMS Payment</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('advance_sms_payments.store') }}">

                    @csrf

                    {{-- amount --}}
                    <div class="form-group">

                        <label for="amount"><span class="text-danger">*</span>Amount</label>

                        <div class="input-group">
                            <input name="amount" type="number" min="50"
                                class="form-control @error('amount') is-invalid @enderror" id="amount" value="100"
                                required>
                            <div class="input-group-append">
                                <span class="input-group-text">{{ config('consumer.currency') }}</span>
                            </div>
                        </div>

                        @error('amount')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror

                    </div>
                    {{-- amount --}}

                    {{-- payment_gateway_id --}}
                    <div class="form-group">
                        <label for="payment_gateway_id" class="sr-only">Payment Gateway</label>

                        <select id="payment_gateway_id" name="payment_gateway_id" class="form-control form-control-sm"
                            required>

                            <option value="">Pay With...</option>

                            @foreach ($payment_gateways as $payment_gateway)

                            <option value="{{ $payment_gateway->id }}">
                                {{ $payment_gateway->payment_method }}
                            </option>

                            @endforeach

                        </select>

                    </div>
                    {{-- payment_gateway_id --}}

                    <button type="submit" class="btn btn-dark">SUBMIT</button>

                </form>

            </div>
            <!--/col-sm-6-->

        </div>
        <!--/row-->

    </div>

</div>

@endsection

@section('pageJs')
@endsection
