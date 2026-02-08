@section('contentTitle')
<h3>Send Payment Link</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <form action="{{ route('customer.send-payment-link.store', ['customer' => $customer->id]) }}" method="POST">

            @csrf
            <!--name-->
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" value="{{ $customer->name }}" disabled>
            </div>
            <!--/name-->
            <!--username-->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" value="{{ $customer->username }}" disabled>
            </div>
            <!--/username-->
            <!--mobile-->
            <div class="form-group">
                <label for="mobile">Mobile Number</label>
                <input type="text" class="form-control" id="mobile" value="{{ $customer->mobile }}" disabled>
            </div>
            <!--/mobile-->
            <!--message-->
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" name="message" aria-describedby="length" rows="3"
                    onkeyup="charcountupdate(this.value)"
                    required>Dear Valued Customer, Please pay your dues at : {{ route('root') }}?cid={{ $bill->customer_id }}&bid={{ $bill->id }}</textarea>
                <small id="length" class="form-text text-muted"></small>
            </div>
            <!--message-->
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

    </div>

</div>

@endsection

@section('pageJs')

<script>
    function charcountupdate(str) {
        var lng = str.length;
        document.getElementById("length").innerHTML = lng + ' character';
    }
</script>

@endsection
