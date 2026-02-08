@section('contentTitle')
<h3> Edit Billing Profile </h3>
@endsection

@section('content')

<div class="row">

    {{-- Left Column --}}
    <div class="col-sm-6">

        <div class="card">

            <div class="card-body">

                <form method="POST"
                    action="{{ route('customer-billing-profile-edit.update', ['customer' => $customer->id]) }}">

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
                        <input type="text" class="form-control" id="mobile" value="{{ $customer->mobile }}" disabled>
                    </div>
                    <!--/mobile-->

                    <!--Billing Profile-->
                    <div class="form-group">
                        <label for="billing_profile">Active Profile</label>
                        <input type="text" class="form-control" id="billing_profile" value="{{ $active_profile->name }}"
                            disabled>
                    </div>
                    <!--/Billing Profile-->

                    <!--billing_profile_id-->
                    <div class="form-group">
                        <label for="billing_profile_id"><span class="text-danger">*</span>
                            Select New Profile
                        </label>
                        <select class="form-control" id="billing_profile_id" name="billing_profile_id"
                            onchange="showRuntimeInvoice('{{ $customer->id }}', this.value)" required>
                            @foreach ($billing_profiles->sortBy('billing_due_date') as $billing_profile)
                            <option value="{{ $billing_profile->id }}">{{ $billing_profile->due_date_figure }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/billing_profile_id-->

                    <button type="submit" id="btn-submit" class="btn btn-dark">SUBMIT</button>

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

                </div>

            </div>

        </div>

    </div>
    {{-- Right Column --}}

</div>

@endsection

@section('pageJs')

<script>
    function showRuntimeInvoice(customer, billing_profile)
    {
        $("#btn-submit").attr("disabled",true);
        $("#runtime_invoice").html('<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>');
        let url = "/admin/billing-profile-edit-runtime-invoice/" + customer + "/" + billing_profile;
            $.get( url, function( data ) {
            $("#runtime_invoice").html(data);
            $("#btn-submit").attr("disabled",false);
        });
    }
</script>

@endsection