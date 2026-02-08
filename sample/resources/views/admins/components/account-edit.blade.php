@section('contentTitle')
<h3>Edit Account</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item"><a href="{{ $previous_url }}"> {{ $breadcrumb_label }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <form id="quickForm" method="POST" action="{{ route('accounts.update', ['account' => $account->id]) }}">
            @csrf
            @method('put')

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

            <!--cash_out_instruction-->
            <div class="form-group">
                <label for="cash_out_instruction"><span class="text-danger">*</span>Note</label>
                <input name="cash_out_instruction" type="text"
                    class="form-control @error('cash_out_instruction') is-invalid @enderror" id="cash_out_instruction"
                    value="{{ $account->cash_out_instruction }}" required>
                @error('cash_out_instruction')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <!--/cash_out_instruction-->

            <button type="submit" class="btn btn-dark">Submit</button>

        </form>

    </div>

</div>

@endsection

@section('pageJs')
@endsection