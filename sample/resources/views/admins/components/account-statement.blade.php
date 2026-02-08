@section('contentTitle')
<h3>Account Statement</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item"><a href="{{ $previous_url }}">{{ $breadcrumb_label }}</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route('account.transactions', ['account' => $account->id]) }}">Transactions</a></li>
    <li class="breadcrumb-item active">Statement</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <form id="quickForm" method="POST" action="{{ route('accounts.statement.store', ['account' => $account->id]) }}"
            autocomplete="off">
            @csrf

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

            <!--transaction_type-->
            <div class="form-group">
                <label for="transaction_type"><span class="text-danger">*</span>Transaction Type</label>
                <select class="form-control" id="transaction_type" name="transaction_type" required>
                    <option value="">Please select...</option>
                    <option value="cash_in">Cash In</option>
                    <option value="cash_out">Cash Out</option>
                    <option value="all">All</option>
                </select>
            </div>
            <!--/transaction_type-->

            <!--from_date-->
            <div class='form-group'>
                <label for='from_date'><span class="text-danger">*</span>From Date</label>
                <input type='text' name='from_date' id='from_date' class='form-control' required>
            </div>
            <!--/from_date-->

            <!--to_date-->
            <div class='form-group'>
                <label for='to_date'><span class="text-danger">*</span>To Date</label>
                <input type='text' name='to_date' id='to_date' class='form-control' required>
            </div>
            <!--/to_date-->

            <button type="submit" class="btn btn-dark">Download</button>

        </form>

    </div>

</div>

@endsection

@section('pageJs')

<script>
    $(function() {
		$('#from_date').datepicker({
			autoclose: !0
		});
	});

    $(function() {
    	$('#to_date').datepicker({
    		autoclose: !0
    	});
    });
</script>

@endsection