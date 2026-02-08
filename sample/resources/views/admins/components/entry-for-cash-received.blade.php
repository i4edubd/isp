@section('contentTitle')
<h3>Entry for cash received</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item"><a href="{{ route('accounts.receivable') }}">Accounts Receivable</a></li>
    <li class="breadcrumb-item active">Entry for cash received</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <div class="row">

            <div class="col-sm-6">

                <form id="quickForm" method="POST" action="{{ route('entry-for-cash-received.store') }}">

                    @csrf

                    <div class="card-body">

                        <!--Account Owner-->
                        <div class="form-group">
                            <label for="account_owner">Account Owner</label>
                            <input type="text" class="form-control" id="account_owner"
                                value="{{ Auth::user()->company }} ({{ Auth::user()->name }})" readonly>
                        </div>
                        <!--/Account Owner-->

                        <!--Account Provider-->
                        <div class="form-group">
                            <label for="account_id"><span class="text-danger">*</span>Account Provider</label>
                            <select name="account_id" id="account_id" class="form-control" required>
                                <option value="">please select...</option>
                                @foreach ($receivable_accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->provider->company }} ({{
                                    $account->provider->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <!--/Account Provider-->

                        <!--amount-->
                        <div class="form-group">
                            <label for="amount"><span class="text-danger">*</span>Amount Received</label>
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