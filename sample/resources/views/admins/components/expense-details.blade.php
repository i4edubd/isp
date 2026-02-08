<table class="table table-bordered table-striped" style="width:100%;">
    <thead>
        <tr>
            <th>Expense Category</th>
            <th>Sub Category</th>
            <th>Amount</th>
            <th>Note</th>
            <th>Date</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expenses as $expense)
        <tr>
            <td>{{ $expense->category->category_name }}</td>
            <td>{{ $expense->subcategory->expense_subcategory_name }}</td>
            <td>{{ $expense->amount }}</td>
            <td>{{ $expense->note }}</td>
            <td>{{ $expense->expense_date }}</td>
            <td class="d-sm-flex">
                <a class="btn btn-info btn-sm" href="{{ route('expenses.edit', ['expense' => $expense->id]) }}">
                    <i class="fas fa-pencil-alt"></i>
                    Edit
                </a>

                <form method="post" action="{{ route('expenses.destroy', ['expense' => $expense->id]) }}"
                    onsubmit="return confirm('Are you sure to Delete')">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i>Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
