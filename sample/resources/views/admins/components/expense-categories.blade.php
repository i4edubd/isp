@section('contentTitle')

<ul class="nav flex-column flex-sm-row">
    <!--New Expense Category-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('expense_categories.create') }}">
            <i class="fas fa-plus"></i>
            New Expense Category
        </a>
    </li>
    <!--/New Expense Category-->
</ul>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <!--modal -->
        <div class="modal fade" id="modal-default">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Expense Sub Category</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="ModalBody">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /modal-content -->
            </div>
            <!-- /modal-dialog -->
        </div>
        <!-- /modal -->

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Expense Category</th>
                    <th scope="col">Subcategories</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expense_categories as $expense_category)
                <tr>
                    <td scope="row">{{ $expense_category->id }}</td>
                    <td>{{ $expense_category->category_name }}</td>
                    <td>
                        @foreach ($expense_category->subcategories->where('hidden','no') as $subcategory)
                        <a href="#" class="border border-info"
                            onclick="showExpenseSubCategory('{{ $subcategory->id }}')">{{
                            $subcategory->expense_subcategory_name }}</a>
                        @endforeach
                    </td>
                    <td class="d-sm-flex">
                        <a class="btn btn-primary btn-sm"
                            href="{{ route('expense_categories.expense_subcategories.create', ['expense_category' => $expense_category->id]) }}">
                            <i class="fas fa-plus"></i>
                            Sub Category
                        </a>
                        <a class="btn btn-info btn-sm"
                            href="{{ route('expense_categories.edit', ['expense_category' => $expense_category->id]) }}">
                            <i class="fas fa-pencil-alt"></i>
                            Edit
                        </a>
                        <form method="post"
                            action="{{ route('expense_categories.destroy', ['expense_category' => $expense_category->id]) }}"
                            onsubmit="return confirm('Are you sure to Delete')">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash"></i>Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@endsection

@section('pageJs')
<script>
    function showExpenseSubCategory(expense_subcategory) {
        $.get( "/admin/expense_subcategories/" + expense_subcategory, function( data ) {
            $("#ModalBody").html(data);
            $('#modal-default').modal('show');
        });
    }
</script>

@endsection
