@section('contentTitle')
<h3>Advance Payment</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('customers.advance_payment.store', ['customer' => $customer->id]) }}" method="POST">

            @csrf

            <!--name-->
            <div class="form-group">
                <label for="name">Customer Name</label>
                <input type="text" class="form-control" id="name" value="{{ $customer->name }}" disabled>
            </div>
            <!--/name-->

            <!--username-->
            <div class="form-group">
                <label for="username">Customer Username</label>
                <input type="text" class="form-control" id="username" value="{{ $customer->username }}" disabled>
            </div>
            <!--/username-->

            <!--mobile-->
            <div class="form-group">
                <label for="mobile">Customer Mobile</label>
                <input type="text" class="form-control" id="mobile" value="{{ $customer->mobile }}" disabled>
            </div>
            <!--/mobile-->

            <!--advance_payment-->
            <div class="form-group">
                <label for="advance_payment">Advance Payment ({{ config('consumer.currency') }})</label>
                <input type="text" class="form-control" id="advance_payment" value="{{ $customer->advance_payment }}"
                    disabled>
            </div>
            <!--/advance_payment-->

            <!--new_payment-->
            <div class="form-group">
                <label for="new_payment">New Payment</label>
                <input type="text" class="form-control" id="new_payment" name="new_payment" required>
            </div>
            <!--new_payment-->

            <button type="submit" class="btn btn-primary">Submit</button>

        </form>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
