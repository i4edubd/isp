@section('contentTitle')
<h3> New Expense Category </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST" action="{{ route('expense_categories.store') }}">

            @csrf
            <div class="col-sm-6">

                <!--category_name-->
                <div class="form-group">
                    <label for="category_name"><span class="text-danger">*</span>Category Name</label>
                    <input name="category_name" type="text"
                        class="form-control @error('category_name') is-invalid @enderror" id="category_name"
                        value="{{ old('category_name') }}" required>

                    @error('category_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/category_name-->

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
