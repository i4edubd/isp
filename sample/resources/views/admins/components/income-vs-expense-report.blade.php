@section('contentTitle')
<h4>Income Vs. Expense Report</h4>
@endsection

@section('content')

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <form class="form-inline my-2 my-lg-0" action="{{ route('incomes-vs-expenses.index') }}" method="get">
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

<div class="card">

    <div class="card-body">

        <table class="table">

            <thead>
                <tr>
                    <th scope="col">Year</th>
                    <th scope="col">Month</th>
                    <th scope="col">Income</th>
                    <th scope="col">Expense</th>
                    <th scope="col">(Income - Expense)</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($summary_report as $row)
                <tr>
                    <td>{{ $row['year'] }}</td>
                    <td>{{ $row['month'] }}</td>
                    <td>{{ $row['income'] }}</td>
                    <td>{{ $row['expense'] }}</td>
                    <td>{{ $row['balance'] }}</td>
                </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
