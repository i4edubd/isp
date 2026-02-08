@section('contentTitle')
<h3> Edit Customer's Bill </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="col-sm-6">

            <form method="POST" action="{{ route('customer_bills.update', ['customer_bill' => $customer_bill->id ]) }}">

                @csrf

                @method('put')

                <!--amount-->
                <div class="form-group">

                    <label for="amount"><span class="text-danger">*</span>Amount</label>

                    <input name="amount" type="text" class="form-control" id="amount"
                        value="{{ $customer_bill->amount }}" required>

                </div>
                <!--/amount-->

                <!--description-->
                <div class="form-group">

                    <label for="description"><span class="text-danger">*</span>Description</label>

                    <input name="description" type="text" class="form-control" id="description"
                        value="{{ $customer_bill->description }}" required>

                </div>
                <!--/description-->

                <!--billing_period-->
                <div class="form-group">

                    <label for="billing_period"><span class="text-danger">*</span>Billing Period</label>

                    <input name="billing_period" type="text" class="form-control" id="billing_period"
                        value="{{ $customer_bill->billing_period }}" required>

                </div>
                <!--/billing_period-->

                <!--due_date-->
                <div class="form-group">

                    <label for="datepicker"><span class="text-danger">*</span>Due Date</label>

                    <input type="text" id="datepicker" name="due_date" class="form-control"
                        value="{{ $customer_bill->due_date }}" required>

                </div>
                <!--/due_date-->

                <button type="submit" class="btn btn-dark">Submit</button>

            </form>

        </div>

    </div>

</div>

@endsection

@section('pageJs')

<script>
    $(function() {
        $('#datepicker').datepicker({
            autoclose: !0
        });
    });

</script>

@endsection
