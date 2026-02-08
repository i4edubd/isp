<dl class="row">
    <dt class="col-sm-4">Expense Category: </dt>
    <dd class="col-sm-8">{{ $expense_subcategory->category->category_name }}</dd>
</dl>

<hr>

<dl class="row">
    <dt class="col-sm-4">Expense Sub Category: </dt>
    <dd class="col-sm-8">{{ $expense_subcategory->expense_subcategory_name }}</dd>
</dl>

<hr>

<div class="d-flex flex-row">

    <a class="btn btn-primary btn-sm"
        href="{{ route('expense_subcategories.edit', ['expense_subcategory' => $expense_subcategory->id]) }}"
        role="button">EDIT</a>


    <form method="post"
        action="{{ route('expense_subcategories.destroy', ['expense_subcategory' => $expense_subcategory->id]) }}"
        onsubmit="return confirm('Are you sure to Delete')">
        @csrf
        @method('delete')
        <button type="submit" class="btn btn-danger btn-sm">DELETE</button>
    </form>

</div>
