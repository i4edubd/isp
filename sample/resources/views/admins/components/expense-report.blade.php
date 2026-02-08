@section('contentTitle')
<h4>Expense Report</h4>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <!--modal -->
        <div class="modal fade" id="modal-default">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Expense Deatils</h4>
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

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <form class="form-inline my-2 my-lg-0" action="{{ route('expense.report') }}" method="get">
                <select name="year" id="year" class="form-control" required="">
                    <option value=''>
                        <--Select Year-->
                    </option>
                    @php
                    $start = date(config('app.year_format'));
                    $stop = $start - 10;
                    @endphp
                    @for($i = $start; $i >= $stop; $i--)
                    <option value="{{$i}}">{{$i}}</option>
                    @endfor
                </select>
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Filter</button>
            </form>
        </nav>

        <!-- Custom Tabs -->
        <div class="card-header d-flex p-0">
            <h3 class="card-title p-3">
                Year: {{ $year }} <span class="text-danger"> | </span>
                Total Expense: {{ $expenses->sum('amount') }} <span class="text-danger"> | </span>
                <a href="{{ route('expense.report.download', ['year' => $year]) }}"> Download Report </a>
            </h3>
            <ul class="nav nav-pills ml-auto p-2">
                <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Yearly Report</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Monthly Report</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_3" data-toggle="tab">Summary Report</a></li>
            </ul>
        </div>
        <!-- /card-header -->

        <div class="tab-content">

            <!-- tab-pane 1-->
            <div class="tab-pane active" id="tab_1">
                <table class="table table-bordered table-striped" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Expense Category</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $expenses->groupBy('expense_category_id') as $expense_category_id =>
                        $expenses_group_by_expense_category )
                        <tr>
                            <td>{{ $year }}</td>
                            <td>
                                <a href="#"
                                    onclick="showExpenseDetails('{{ $expense_category_id }}', '{{ $year }}', '0')">
                                    {{ $expenses_group_by_expense_category->first()->category->category_name }}
                                </a>
                            </td>
                            <td>{{ $expenses_group_by_expense_category->sum('amount') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /tab-pane 1-->

            <!-- tab-pane 2-->
            <div class="tab-pane" id="tab_2">

                @foreach ($expenses->sortByDesc('created_at')->groupBy('month') as $month => $expenses_group_by_month)
                <h3 class="card-title p-3 border border-info">
                    Month: {{ $month }} <span class="text-danger"> | </span>
                    Total Expense: {{ $expenses_group_by_month->sum('amount') }} <span class="text-danger"> | </span>
                    <a href="{{ route('expense.report.download', ['year' => $year, 'month' => $month]) }}">
                        Download Report
                    </a>
                </h3>
                <table class="table table-bordered table-striped" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Expense Category</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $expenses_group_by_month->groupBy('expense_category_id') as $expense_category_id =>
                        $expenses_group_by_expense_category )
                        <tr>
                            <td>{{ $year}}</td>
                            <td>{{ $month }}</td>
                            <td><a href="#"
                                    onclick="showExpenseDetails('{{ $expense_category_id }}', '{{ $year }}', '{{ $month }}')">
                                    {{ $expenses_group_by_expense_category->first()->category->category_name }}
                                </a>
                            </td>
                            <td>{{ $expenses_group_by_expense_category->sum('amount') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endforeach
            </div>
            <!-- /tab-pane 2-->

            <!-- tab-pane -->
            <div class="tab-pane" id="tab_3">
                <table class="table table-bordered table-striped" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Expense Category</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $summary_report as $s_report )
                        <tr>
                            <td>{{ $s_report->year }}</td>
                            <td>{{ $s_report->month }}</td>
                            <td>{{ $s_report->expense_category }}</td>
                            <td>{{ $s_report->amount }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /tab-pane -->

        </div>
        <!-- /tab-content -->

    </div>

</div>

@endsection

@section('pageJs')

<script>
    function showExpenseDetails(expense_category_id,year,month) {
    let url = "/admin/expense/report/details/?expense_category_id="+expense_category_id+"&year="+year+"&month="+month;
    $.get( url, function( data ) {
        $("#ModalBody").html(data);
        $('#modal-default').modal('show');
    });
}
</script>

@endsection
