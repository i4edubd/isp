@section('contentTitle')
<h3>Edit Special Price</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST"
            action="{{ route('customers.custom_prices.update', ['customer' => $customer->id, 'custom_price' => $custom_price->id]) }}">
            @csrf
            @method('put')
            <div class="col-sm-6">

                <!--Customer ID-->
                <div class="form-group">
                    <label for="customer_id">Customer ID</label>
                    <input type="text" class="form-control" id="customer_id" value="{{ $customer->id }}" disabled>
                </div>
                <!--/Customer ID-->

                <!--Customer Name-->
                <div class="form-group">
                    <label for="customer_name">Customer Username</label>
                    <input type="text" class="form-control" id="customer_name" value="{{ $customer->username }}"
                        disabled>
                </div>
                <!--/Customer Name-->

                <!--Package Name-->
                <div class="form-group">
                    <label for="package_name">Package Name</label>
                    <input type="text" class="form-control" id="package_name" value="{{ $custom_price->package->name }}"
                        disabled>
                </div>
                <!--/Package Name-->

                <!--Regular Price-->
                <div class="form-group">
                    <label for="regular_price">Regular Price</label>
                    <input type="text" class="form-control" id="regular_price"
                        value="{{ $custom_price->package->price }} {{ config('consumer.currency') }}" disabled>
                </div>
                <!--/Regular Price-->

                <!--Special Price-->
                <div class="form-group">
                    <label for="special_price"><span class="text-danger">*</span>Special Price</label>

                    <div class="input-group">
                        <input name="special_price" type="text"
                            class="form-control @error('special_price') is-invalid @enderror" id="special_price"
                            value="{{ $custom_price->price }}" required>
                        <div class="input-group-append">
                            <span class="input-group-text">{{ config('consumer.currency') }}</span>
                        </div>
                    </div>

                    @error('special_price')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/special_price-->

            </div>
            <!--/col-sm-6-->

            <div class="col-sm-6">
                <button type="submit" class="btn btn-dark">Submit</button>
            </div>
        </form>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
