@section('contentTitle')
<h3> Edit Service </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title font-weight-bold">Reseller: {{ $operator->name }}</h3>
    </div>

    <form id="quickForm" method="POST"
        action="{{ route('operators.other_services.update', ['operator' => $operator->id , 'other_service' => $other_service->id]) }}">

        @csrf

        @method('put')

        <div class="col-sm-6">

            <!--name-->
            <div class="form-group">
                <label for="name"><span class="text-danger">*</span>Name</label>
                <input name="name" type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    value="{{ $other_service->name }}" required>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/name-->

            <!--price-->
            <div class="form-group">
                <label for="price"><span class="text-danger">*</span>Customer's Price</label>

                <div class="input-group">
                    <input name="price" type="number" class="form-control @error('price') is-invalid @enderror"
                        id="price" value="{{ $other_service->price }}" required>
                    <div class="input-group-append">
                        <span class="input-group-text">{{ config('consumer.currency') }}</span>
                    </div>
                </div>

                @error('price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/price-->

            <!--operator_price-->
            <div class="form-group">
                <label for="operator_price"><span class="text-danger">*</span>Operator's Price</label>

                <div class="input-group">
                    <input name="operator_price" type="number"
                        class="form-control @error('operator_price') is-invalid @enderror" id="operator_price"
                        value="{{ $other_service->operator_price }}" required>
                    <div class="input-group-append">
                        <span class="input-group-text">{{ config('consumer.currency') }}</span>
                    </div>
                </div>

                @error('operator_price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
            <!--/operator_price-->

        </div>
        <!--/col-sm-6-->

        <div class="col-sm-6">
            <button type="submit" class="btn btn-dark">Submit</button>
        </div>

    </form>

</div>

@endsection

@section('pageJs')
@endsection