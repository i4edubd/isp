@section('contentTitle')
<h3>Customer's Custom Field</h3>
@endsection

@section('content')

<div class="card">

    <form id="quickForm" method="POST"
        action="{{ route('customers.custom_attributes.store', ['customer' => $customer->id]) }}">

        @csrf

        <div class="card-body">

            <div class="row">

                <div class="col-sm-6">

                    @foreach ($custom_fields as $custom_field)

                    <div class="form-group">
                        <label for="{{ $custom_field->id }}">{{ $custom_field->name }}</label>
                        <input name="{{ $custom_field->id }}" type="text" class="form-control"
                            id="{{ $custom_field->id }}" value="{{ $custom_field->value }}">
                    </div>

                    @endforeach

                </div>
                <!--/col-sm-6-->
            </div>
            <!--/row-->
        </div>
        <!--/card-body-->

        <div class="card-footer">
            <button type="submit" class="btn btn-dark">SUBMIT</button>
        </div>
        <!--/card-footer-->

    </form>

</div>

@endsection

@section('pageJs')
@endsection
