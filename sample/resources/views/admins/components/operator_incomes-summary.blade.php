@section('contentTitle')
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('operators_incomes_summary.index') }}" method="get">

    {{-- year --}}
    <div class="form-group col-md-2">
        <select name="year" id="year" class="form-control">
            <option value=''>Year...</option>
            @php
            $start = date(config('app.year_format'));
            $stop = $start - 5;
            @endphp
            @for($i = $start; $i >= $stop; $i--)
            <option value="{{$i}}">{{$i}}</option>
            @endfor
        </select>
    </div>
    {{--year --}}

    {{-- month --}}
    <div class="form-group col-md-2">
        <select name="month" id="month" class="form-control">
            <option value=''>Month...</option>
            <option value='January'>January</option>
            <option value='February'>February</option>
            <option value='March'>March</option>
            <option value='April'>April</option>
            <option value='May'>May</option>
            <option value='June'>June</option>
            <option value='July'>July</option>
            <option value='August'>August</option>
            <option value='September'>September</option>
            <option value='October'>October</option>
            <option value='November'>November</option>
            <option value='December'>December</option>
        </select>
    </div>
    {{--month --}}

    {{-- Page length --}}
    <div class="form-group col-md-2">
        <select name="length" id="length" class="form-control">
            <option value="{{ $length }}" selected>Show {{ $length }} entries </option>
            <option value="10">Show 10 entries</option>
            <option value="25">Show 25 entries</option>
            <option value="50">Show 50 entries</option>
            <option value="100">Show 100 entries</option>
        </select>
    </div>
    {{--Page length --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>

{{-- @endFilter --}}

<div class="card card-outline card-success">

    <div class="card-header">
        <h3 class="card-title">Total: {{ $total_amount }} {{ config('consumer.currency') }}</h3>
    </div>
    <!-- /card-header -->

    <div class="card-body">

        <table id="phpPaging" class="table table-bordered">

            <thead>

                <tr>
                    <th scope="col">Year</th>
                    <th scope="col">Month</th>
                    <th scope="col">Amount</th>
                </tr>

            </thead>

            <tbody>

                @foreach ($operator_incomes as $operator_income )

                <tr>
                    <td>{{ $operator_income->year }}</td>
                    <td>{{ $operator_income->month }}</td>
                    <td>{{ $operator_income->amount }}</td>
                </tr>

                @endforeach

            </tbody>

        </table>

    </div>
    <!-- /card-body -->

    <div class="card-footer">

        <div class="row">

            <div class="col-sm-2">
                Total Entries: {{ $operator_incomes->total() }}
            </div>

            <div class="col-sm-6">
                {{ $operator_incomes->links() }}
            </div>

        </div>

    </div>
    <!--/card-footer-->

</div>

@endsection

@section('pageJs')
@endsection
