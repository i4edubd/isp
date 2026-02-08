<!--credit_limit-->
<div class="form-group">
    <label for="credit_limit">Credit Limit (Enter 0 for no limit)</label>

    <div class="input-group">
        <input name="credit_limit" type="text" class="form-control @error('credit_limit') is-invalid @enderror"
            id="credit_limit" value="0">
        <div class="input-group-append">
            <span class="input-group-text">{{ config('consumer.currency') }}</span>
        </div>
    </div>

    @error('credit_limit')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror

</div>
<!--/credit_limit-->
