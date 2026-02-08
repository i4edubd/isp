<div class="row">
    <div class="col-sm">
        <div class="callout callout-info">
            <dl>
                <dt>
                    By setting Show Payment Breakdown = Yes
                </dt>
                <dd>
                    <ol>
                        <li>
                            You, your reseller and your sub-reseller can view payment distributions.
                        </li>
                    </ol>
                </dd>
            </dl>
        </div>
    </div>
    <div class="col-sm">
        <div class="callout callout-info">
            <dl>
                <dt>
                    By setting Show Payment Breakdown = No
                </dt>
                <dd>
                    <ol>
                        <li>
                            You, your reseller and your sub-reseller can not view payment distributions.
                        </li>
                    </ol>
                </dd>
            </dl>
        </div>
    </div>
</div>

<form class="" action="{{ route('customer-payment-breakdown.store') }}" method="POST">
    @csrf

    <!--show_payment_breakdown-->
    <div class="form-group">
        <label for="show_payment_breakdown"><span class="text-danger">*</span>Show Payment Breakdown</label>
        <select class="form-control" id="show_payment_breakdown" name="show_payment_breakdown" required>
            <option selected>{{ Auth::user()->show_payment_breakdown }}</option>
            <option value="yes">yes</option>
            <option value="no">no</option>
        </select>
        @error('show_payment_breakdown')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <!--/show_payment_breakdown-->

    <div class="form-check mt-2">
        <button type="submit" class="btn btn-dark">
            SUBMIT
        </button>
    </div>

</form>
