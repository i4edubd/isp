@section('contentTitle')
    <h3> Create Bill/Invoice </h3>
@endsection

@section('content')
    <div class="card">

        <div class="row">

            {{-- Left Col --}}
            <div class="col-sm-6">

                <div class="card">

                    <div class="card-body">

                        <form action="{{ route('customers.customer_bills.store', ['customer' => $customer->id]) }}"
                            method="POST">

                            @csrf

                            <!--name-->
                            <div class="form-group">
                                <label for="name">Customer Name</label>
                                <input type="text" class="form-control" id="name" value="{{ $customer->name }}"
                                    readonly>
                            </div>
                            <!--/name-->

                            <!--mobile-->
                            <div class="form-group">
                                <label for="mobile">Customer Mobile</label>
                                <input type="text" class="form-control" id="mobile" value="{{ $customer->mobile }}"
                                    readonly>
                            </div>
                            <!--/mobile-->

                            <!--billing_period-->
                            <div class="form-group">
                                <label for="billing_period"><span class="text-danger">*</span>Billing Period</label>
                                <div class="input-group">
                                    <select class="form-control" id="billing_period" name="billing_period" required
                                        onchange="showRuntimeInvoice('{{ $customer->id }}', this.value)">
                                        @foreach ($billing_periods as $billing_period)
                                            <option value="{{ $billing_period }}">{{ $billing_period }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--/billing_period-->

                            <button type="submit" class="btn btn-primary" id="btn-submit">Submit</button>

                        </form>

                    </div>

                </div>

            </div>
            {{-- Left Col --}}

            {{-- Right Col --}}
            <div class="col-sm-6">

                <div class="card">

                    <div class="card-body">

                        <p>Generated Bill/Invoice</p>

                        <div id="runtime_invoice">

                            @include('admins.components.runtime-invoice')

                        </div>

                    </div>

                </div>

            </div>
            {{-- Right Col --}}

        </div>

    </div>
@endsection

@section('pageJs')
    <script>
        function showRuntimeInvoice(customer_id, billing_period) {
            $("#btn-submit").attr("disabled", true);
            $("#runtime_invoice").html('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
            let url = "/ajax/show-runtime-invoice-for-generate-bill-action/" + customer_id + "/" + billing_period;
            $.get(url, function(data) {
                $("#runtime_invoice").html(data);
                $("#btn-submit").attr("disabled", false);
            });
        }
    </script>
@endsection
