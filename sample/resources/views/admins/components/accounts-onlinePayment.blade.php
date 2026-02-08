@section('contentTitle')
<h3>Online Payment <i class="fas fa-money-check-edit-alt"></i></h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item"><a href="{{ route('accounts.payable') }}">Accounts Payable</a></li>
    <li class="breadcrumb-item active">Online Payment</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <div class="row">

            <div class="col-sm-6">

                <form id="quickForm" method="POST"
                    action="{{ route('accounts.OnlinePayment.store', ['account' => $account ]) }}">

                    @csrf

                    <div class="card-body">

                        <!--Account Provider-->
                        <div class="form-group">
                            <label for="account_provider">Account Provider</label>
                            <input type="text" class="form-control" id="account_provider"
                                value="{{ $account->provider->company }} ({{ $account->provider->name }})" readonly>
                        </div>
                        <!--/Account Provider-->

                        <!--Account Owner-->
                        <div class="form-group">
                            <label for="account_owner">Account Owner</label>
                            <input type="text" class="form-control" id="account_owner"
                                value="{{ $account->owner->company }} ({{ $account->owner->name }})" readonly>
                        </div>
                        <!--/Account Owner-->

                        <!--Account Balance-->
                        <div class="form-group">
                            <label for="account_balance">Account Balance</label>
                            <input type="text" class="form-control" id="account_balance" value="{{ $account->balance }}"
                                readonly>
                        </div>
                        <!--/Account Balance-->

                        <!--amount-->
                        <div class="form-group">
                            <label for="amount"><span class="text-danger">*</span>Amount to Pay</label>
                            <input name="amount" type="text" class="form-control @error('amount') is-invalid @enderror"
                                id="amount" value="{{ old('amount') }}" required>
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <!--/amount-->

                        {{-- payment_gateway_id --}}
                        <div class="form-group">

                            <label for="payment_gateway_id">Payment Gateway</label>

                            <select id="payment_gateway_id" name="payment_gateway_id" class="form-control" required>

                                <option value="">Pay With...</option>

                                @foreach ($payment_gateways as $payment_gateway)

                                <option value="{{ $payment_gateway->id }}">
                                    {{ $payment_gateway->payment_method }}
                                </option>

                                @endforeach

                            </select>

                        </div>
                        {{-- payment_gateway_id --}}

                    </div>
                    <!--/card-body-->

                    <div class="card-footer">
                        <button type="submit" class="btn btn-dark">Submit</button>
                    </div>
                    <!--/card-footer-->

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