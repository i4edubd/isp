<!--account_balance-->
<div class="form-group">
    <label for="account_balance">Account Balance</label>

    <div class="input-group">
        <input name="account_balance" type="text" class="form-control @error('account_balance') is-invalid @enderror"
            id="account_balance" value="0">
        <div class="input-group-append">
            <span class="input-group-text">{{ config('consumer.currency') }}</span>
        </div>
    </div>

    @error('account_balance')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror

</div>
<!--/account_balance-->
