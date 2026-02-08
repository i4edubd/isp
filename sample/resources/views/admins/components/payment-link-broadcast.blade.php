@section('contentTitle')
<h3> Payment Link Broadcast </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">


                <form method="POST" action="{{ route('payment-link-broadcast.store') }}">

                    @csrf

                    <!--billing_profile_id-->
                    <div class="form-group">
                        <label for="billing_profile_id">Billing Profile (Optional)</label>
                        <select class="form-control" id="billing_profile_id" name="billing_profile_id"
                            onchange="showCount(this.value)">
                            <option value="">Please select...</option>
                            @foreach (Auth::user()->billing_profiles->sortBy('name') as $billing_profile)
                            <option value="{{ $billing_profile->id }}">{{ $billing_profile->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--/billing_profile_id-->

                    <!--customers_count-->
                    <div class="form-group">

                        <label for="customers_count">Number of Recipient</label>

                        <input type="text" class="form-control" id="customers_count" value="{{ $customers_count }}"
                            disabled>

                    </div>
                    <!--/customers_count-->

                    <!--text_message-->
                    <div class="form-group">
                        <label for="text_message">Text Message (Modify if you need)</label>
                        <input type="text" class="form-control" id="text_message" name="text_message"
                            value="{{ getLocaleString( Auth::user()->id, 'Dear Valued Customer, Please pay your dues at') }}"
                            required>
                    </div>
                    <!--/text_message-->

                    <!--Payment Link-->
                    <div class="form-group">
                        <input type="text" class="form-control"
                            value="{{ route('root') }}?cid={{ $bill->customer_id }}&bid={{ $bill->id }}" disabled>
                    </div>
                    <!--/Payment Link-->

                    <button type="submit" class="btn btn-dark">SUBMIT</button>

                </form>

            </div>
            <!--/col-sm-6-->
        </div>
        <!--/row-->
    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')
<script>
    function showCount(billing_profile) {

        let profile_id;

        profile_id = parseInt(billing_profile);

        if(profile_id > 0){
            let url = "/admin/payment-link-broadcast/" + profile_id + "/edit";
            $.ajax({
                url: url
            }).done(function (data) {
                $("#customers_count").val(data);
            });
        }
    }
</script>
@endsection