@section('content')

<div class="card">

    <div class="card-body">

        <div class="row">

            <div class="col-sm-6">

                <form method="POST" action="{{ route('customers.make_child.store', ['customer' => $customer]) }}">

                    @csrf

                    <!--parent_id-->
                    <div class="form-group">
                        <label for="parent_id">Parent Customer ID/Username</label>
                        <input name="parent_id" type="text" class="form-control" id="parent_id"
                            value="{{ old('parent_id') }}">
                    </div>
                    <!--/parent_id-->

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
@endsection