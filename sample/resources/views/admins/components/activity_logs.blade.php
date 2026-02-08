@section('contentTitle')
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('activity_logs.index') }}" method="get">

    {{-- operator --}}
    <div class="form-group col-md-2">
        <select name="operator_id" id="operator_id" class="form-control">
            <option value=''>operator...</option>
            @foreach (Auth::user()->group_operators->push(Auth::user())->unique() as $operator)
            <option value='{{ $operator->id }}'>{{ $operator->name }}</option>
            @endforeach
        </select>
    </div>
    {{--operator --}}

    {{-- topic --}}
    <div class="form-group col-md-2">
        <select name="topic" id="topic" class="form-control">
            <option value=''>topic...</option>
            @foreach ($activity_logs->pluck('topic')->unique() as $item)
            <option value='{{ $item }}'>{{ $item }}</option>
            @endforeach
        </select>
    </div>
    {{--topic --}}

    {{-- year --}}
    <div class="form-group col-md-2">
        <select name="year" id="year" class="form-control">
            <option value=''>year...</option>
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
            <option value=''>month...</option>
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

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>
{{-- @endFilter --}}

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>Operator ID</th>
                    <th>Customer ID</th>
                    <th>Topic</th>
                    <th>Log</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activity_logs as $activity_log)
                <tr>
                    <td>{{ $activity_log->operator_id }}</td>
                    <td>{{ $activity_log->customer_id }}</td>
                    <td>{{ $activity_log->topic }}</td>
                    <td>{{ $activity_log->log }}</td>
                    <td>{{ $activity_log->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@endsection

@section('pageJs')
@endsection