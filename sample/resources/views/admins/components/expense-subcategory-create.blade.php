@section('contentTitle')
Create Expense Sub Category for : <span class="font-weight-bold"> {{ $expense_category->category_name }} </span>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <form id="quickForm" method="POST"
            action="{{ route('expense_categories.expense_subcategories.store', ['expense_category' => $expense_category->id]) }}">

            @csrf
            <div class="col-sm-6">

                <!--expense_subcategory_name-->
                <div class="form-group">
                    <label for="expense_subcategory_name"><span class="text-danger">*</span>Sub Category Name</label>
                    <input name="expense_subcategory_name" type="text"
                        class="form-control @error('expense_subcategory_name') is-invalid @enderror"
                        id="expense_subcategory_name" value="{{ old('expense_subcategory_name') }}" required>

                    @error('expense_subcategory_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/expense_subcategory_name-->

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
