@section('contentTitle')
<h3>Send Money</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item"><a href="{{ $previous_url }}"> {{ $breadcrumb_label }}</a></li>
    <li class="breadcrumb-item active">Send Money</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <div class="row">

            <div class="col-sm-6">

                <form id="quickForm" method="POST"
                    action="{{ route('pending_transactions.store',['account' => $account->id]) }}">

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
                            <label for="account_balance"><span class="text-danger">*</span>Account Balance</label>
                            <input type="text" class="form-control" id="account_balance" value="{{ $account->balance }}"
                                readonly>
                        </div>
                        <!--/Account Balance-->

                        <!--amount-->
                        <div class="form-group">
                            <label for="amount"><span class="text-danger">*</span>Amount</label>
                            <input name="amount" type="text" class="form-control @error('amount') is-invalid @enderror"
                                id="amount" value="{{ old('amount') }}" required>
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <!--/amount-->

                        <!--note-->
                        <div class="form-group">
                            <label for="note">Note</label>
                            <input name="note" type="text" class="form-control @error('note') is-invalid @enderror"
                                id="note" value="{{ old('note') }}">
                            @error('note')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <!--/note-->

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