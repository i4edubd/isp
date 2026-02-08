@section('contentTitle')
<h3>Edit Expense</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <p class="text-danger">* required field</p>

        <div class='col-md-6'>

            <form id="quickForm" method="POST" action="{{ route('expenses.update', ['expense' => $expense->id]) }}">
                @csrf
                @method('put')

                <!--expense_category_id-->
                <div class="form-group">
                    <label for="expense_category_id"><span class="text-danger">*</span>Expense Category</label>
                    <select class="form-control @error('expense_category_id') is-invalid @enderror"
                        id="expense_category_id" name="expense_category_id" required
                        onchange="showSubCategoryOptions(this.value)">
                        <option value="{{ $expense->expense_category_id }}" selected>{{
                            $expense->category->category_name }}
                        </option>
                        @foreach ($expense_categories as $expense_category)
                        <option value="{{ $expense_category->id }}">{{ $expense_category->category_name }}</option>
                        @endforeach
                    </select>

                    @error('expense_category_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!--/expense_category_id-->

                <!--expense_subcategory_id-->
                <div class="form-group">
                    <label for="expense_subcategory_id">Expense Sub Category</label>
                    <select class="form-control" id="expense_subcategory_id" name="expense_subcategory_id">
                        <option value="{{ $expense->expense_subcategory_id }}" selected>
                            {{ $expense->subcategory->expense_subcategory_name }}</option>
                    </select>
                </div>
                <!--/expense_subcategory_id-->


                <!--amount-->
                <div class="form-group">
                    <label for="amount"><span class="text-danger">*</span>Amount</label>
                    <input name="amount" type="text" class="form-control @error('amount') is-invalid @enderror"
                        id="amount" value="{{ $expense->amount }}" required>
                    @error('amount')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <!--/amount-->

                <!--note-->
                <div class="form-group">
                    <label for="note">Note</label>
                    <input name="note" type="text" class="form-control" id="note" value="{{ $expense->note }}">
                </div>
                <!--/note-->

                <!-- Date -->
                <div class="form-group">
                    <label for="expense_date"><span class="text-red">*</span>Date</label>
                    <input type="text" id="expense_date" name="expense_date"
                        class="form-control @error('expense_date') is-invalid @enderror" data-inputmask-alias="datetime"
                        data-inputmask-inputformat="dd-mm-yyyy" value="{{ $expense->expense_date }}" data-mask
                        required="">

                    @error('expense_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror

                </div>
                <!-- /Date -->

                <button type="submit" class="btn btn-dark">Submit</button>

            </form>

        </div>

    </div>

</div>

@endsection

@section('pageJs')

<script>
    //Initialize Select2 Elements
    $('.select2').select2();

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd-mm-yyyy', { 'placeholder': 'dd-mm-yyyy' });
    $('[data-mask]').inputmask();

    function showSubCategoryOptions(expense_category) {
        $.get( "/admin/expense_categories/" + expense_category + "/expense_subcategories", function( data ) {
            $("#expense_subcategory_id").html(data);
        });
    }
</script>

@endsection
