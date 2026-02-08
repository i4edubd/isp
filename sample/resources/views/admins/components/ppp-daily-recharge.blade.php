@section('contentTitle')
    <h3>Recharge/Package Change</h3>
@endsection

@section('content')
    <div class="row">

        {{-- Left Column --}}
        <div class="col-sm-6">

            <div class="card">

                <div class="card-body">

                    <form method="POST" action="{{ route('ppp-daily-recharge.update', ['customer' => $customer->id]) }}"
                        onsubmit="return disableDuplicateSubmit()">

                        @method('put')

                        @csrf

                        <!--name-->
                        <div class="form-group">
                            <label for="name">Customer Name</label>
                            <input type="text" class="form-control" id="name" value="{{ $customer->name }}" disabled>
                        </div>
                        <!--/name-->

                        <!--mobile-->
                        <div class="form-group">
                            <label for="mobile">Customer Mobile</label>
                            <input type="text" class="form-control" id="mobile" value="{{ $customer->mobile }}"
                                disabled>
                        </div>
                        <!--/mobile-->

                        <!--Billing Profile-->
                        <div class="form-group">
                            <label for="billing_profile">Billing Profile</label>
                            <input type="text" class="form-control" id="billing_profile"
                                value="{{ $billing_profile->name }}" disabled>
                        </div>
                        <!--/Billing Profile-->

                        <!--Current Package & Validity -->
                        <div class="form-group">
                            <label for="package_validity">Current Package & Validity</label>
                            <input type="text" class="form-control" id="package_validity"
                                value="{{ $active_package->name }} | {{ $customer->package_expired_at }}" disabled>
                        </div>
                        <!--/Current Package & Validity -->

                        <!--package_id-->
                        <div class="form-group">
                            <label for="package_id"><span class="text-danger">*</span>Select Package</label>
                            <select class="form-control" id="package_id" name="package_id" required
                                onchange="showRuntimeInvoice('{{ $customer->id }}')">
                                <option value="{{ $active_package->id }}" selected>{{ $active_package->name }}</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--/package_id-->

                        <!--validity-->
                        <div class="form-row">
                            <div class="form-group col">
                                <label for="validity_day"><span class="text-danger">*</span>Validity</label>
                                <div class="input-group">
                                    <input name="validity_day" type="number"
                                        min="{{ $billing_profile->minimum_validity }}"
                                        class="form-control @error('validity_day') is-invalid @enderror" id="validity_day"
                                        value="{{ $billing_profile->minimum_validity }}" required
                                        oninput="showRuntimeInvoice('{{ $customer->id }}')">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Days</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col">
                                <label for="validity_hour">-</label>
                                <div class="input-group">
                                    <input name="validity_hour" type="number" max="23" min="0"
                                        class="form-control @error('validity_hour') is-invalid @enderror" id="validity_hour"
                                        value="0" required oninput="showRuntimeInvoice('{{ $customer->id }}')">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/validity-->

                        <button type="submit" id="submit-button" class="btn btn-dark">SUBMIT</button>

                    </form>

                </div>

            </div>

        </div>
        {{-- Left Column --}}

        {{-- Right Column --}}
        <div class="col-sm-6">

            <div class="card">

                <div class="card-body">

                    <p>Generated Bill/Invoice</p>

                    <div id="runtime_invoice">

                        @include('admins.components.runtime-invoice-v2')

                    </div>

                </div>

            </div>

        </div>
        {{-- Right Column --}}

    </div>
@endsection

@section('pageJs')
    <script>
        function showRuntimeInvoice(customer) {
            $("#btn-submit").attr("disabled", true);
            $("#runtime_invoice").html('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
            let package = $("#package_id").val();
            let validity_day = $("#validity_day").val();
            let validity_hour = $("#validity_hour").val();
            let url = "/admin/ppp-daily-recharge-runtime-invoice/" + customer + "/" + package + "?day=" + validity_day +
                "&hour=" + validity_hour;
            $.get(url, function(data) {
                $("#runtime_invoice").html(data);
                $("#btn-submit").attr("disabled", false);
            });
        }
    </script>
@endsection
