@section('contentTitle')
    <h3> Package Change </h3>
@endsection

@section('content')
    <div class="row">

        {{-- Left Column --}}
        <div class="col-sm-6">

            <div class="card">

                <div class="card-body">

                    <form method="POST" action="{{ route('hotspot-package-change.update', ['customer' => $customer->id]) }}"
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
                                onchange="showRuntimeInvoice('{{ $customer->id }}', this.value)">
                                <option value="{{ $active_package->id }}" selected>{{ $active_package->name }}</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!--/package_id-->

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
        function showRuntimeInvoice(customer_id, package) {
            $("#submit-button").attr("disabled", true);
            $("#runtime_invoice").html('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
            let url = "/admin/hotspot-package-change/" + customer_id + "/" + package;
            $.get(url, function(data) {
                $("#runtime_invoice").html(data);
                $("#submit-button").attr("disabled", false);
            });
        }
    </script>
@endsection
