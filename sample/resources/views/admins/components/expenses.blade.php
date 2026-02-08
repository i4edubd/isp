@section('contentTitle')
@endsection

@section('content')

<form action="{{route('expenses.index')}}" method="get">

    <ul class="nav flex-sm-row">

        <!--New Expense-->
        <li class="nav-item mr-4">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('expenses.create') }}">
                <i class="fas fa-plus"></i>
                New Expense
            </a>
        </li>
        <!--/New Expense-->


        <!--expense_date-->
        <li class="nav-item">
            <input type="text" name="expense_date" class="form-control" data-inputmask-alias="datetime"
                data-inputmask-inputformat="dd-mm-yyyy" data-mask required="">
        </li>
        <!--/expense_date-->

        <li class="nav-item">
            <button type="submit" class="btn btn-primary">FILTER</button>
        </li>

    </ul>

</form>

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered table-striped" style="width:100%;">
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

                        {{-- Delete --}}
                        @can('delete', $expense)
                        <form method="post" action="{{ route('expenses.destroy', ['expense' => $expense->id]) }}"
                            onsubmit="return confirm('Are you sure to Delete')">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-sm"><i
                                    class="fas fa-trash"></i>Delete</button>
                        </form>
                        @endcan
                        {{-- Delete --}}

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
    //Initialize Select2 Elements
    $('.select2').select2();

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    });

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd-mm-yyyy', {
        'placeholder': 'dd-mm-yyyy'
    });

    $('[data-mask]').inputmask();

</script>
@endsection
