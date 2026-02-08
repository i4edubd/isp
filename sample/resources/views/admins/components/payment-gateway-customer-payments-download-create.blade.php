@section('contentTitle')
<h3>Payments Download </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST"
                    action="{{ route('payment_gateways.customer_payments.store', ['payment_gateway' => $payment_gateway->id]) }}">

                    @csrf

                    <!--Payment Gateway-->
                    <div class="form-group">
                        <label for="payment_gateway">Payment Gateway</label>
                        <input type="text" class="form-control" id="payment_gateway"
                            value="{{ $payment_gateway->payment_method }}" readonly>
                    </div>
                    <!--/Payment Gateway-->

                    {{-- year --}}
                    <div class="form-group">
                        <label for="year">year</label>
                        <select name="year" id="year" class="form-control">
                            <option value=''>please select...</option>
                            @php
                            $start = date(config('app.year_format'));
                            $stop = $start - 5;
                            @endphp
                            @for($i = $start; $i >= $stop; $i--)
                            <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                    {{--year --}}

                    {{-- month --}}
                    <div class="form-group">
                        <label for="month">Month</label>
                        <select name="month" id="month" class="form-control">
                            <option value=''>please select...</option>
                            <option value='January'>January</option>
                            <option value='February'>February</option>
                            <option value='March'>March</option>
                            <option value='April'>April</option>
                            <option value='May'>May</option>
                            <option value='June'>June</option>
                            <option value='July'>July</option>
                            <option value='August'>August</option>
                            <option value='September'>September</option>
                            <option value='October'>October</option>
                            <option value='November'>November</option>
                            <option value='December'>December</option>
                        </select>
                    </div>
                    {{--month --}}

                    <button type="submit" class="btn btn-dark">Download</button>

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
@endsection