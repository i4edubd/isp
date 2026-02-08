@section('contentTitle')
<h3>Accounts Monthly Report</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item active">Monthly Report</li>
</ol>
@endsection

@section('content')

{{-- Filter --}}
<div class="card">
    <div class="card-body">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <form class="form-inline my-2 my-lg-0" action="{{ route('accounts.monthly-report') }}" method="get">

                {{-- Year --}}
                <div class="mr-2">
                    <select name="year" id="year" class="form-control" required>
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
                </div>
                {{-- Year --}}

                {{-- month --}}
                <div class="mr-2">
                    <select name="month" id="month" class="form-control" required>
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

                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Filter</button>
            </form>
        </nav>
    </div>
</div>
{{-- Filter --}}

{{-- Receivable Accounts --}}
<div class="card card-outline card-dark">
    <div class="card-header">
        <h3 class="card-title">Receivable Accounts</h3>
        <span class="border border-success m-2 pl-1 pr-1">
            Total In : {{ $receivable_collection->sum('cash_in') }}
        </span>
        <span class="border border-dark m-2 pl-1 pr-1">
            Total Out : {{ $receivable_collection->sum('cash_outs') }}
        </span>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Account Owner</th>
                    <th scope="col">Account Provider</th>
                    <th scope="col">Year</th>
                    <th scope="col">Month</th>
                    <th scope="col">Cash In</th>
                    <th scope="col">Cash Out</th>
                    <th scope="col">Current Balance</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($receivable_collection as $item)
                <tr>
                    <td>{{ $item->get('account_owner') }}</td>
                    <td>{{ $item->get('account_provider') }}</td>
                    <td>{{ $item->get('year') }}</td>
                    <td>{{ $item->get('month') }}</td>
                    <td>{{ $item->get('cash_in') }}</td>
                    <td>{{ $item->get('cash_outs') }}</td>
                    <td>{{ $item->get('balance') }}</td>
                    <td>
                        <a class="btn btn-outline-info btn-sm"
                            href="{{ route('account.transactions',['account' => $item->get('account_id')]) }}">
                            <i class="fas fa-exchange-alt"></i>
                            Transactions
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{-- Receivable Accounts --}}

{{-- Payable Accounts --}}
<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">Payable Accounts</h3>
        <span class="border border-success m-2 pl-1 pr-1">
            Total In : {{ $payable_collection->sum('cash_in') }}
        </span>
        <span class="border border-dark m-2 pl-1 pr-1">
            Total Out : {{ $payable_collection->sum('cash_outs') }}
        </span>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Account Owner</th>
                    <th scope="col">Account Provider</th>
                    <th scope="col">Year</th>
                    <th scope="col">Month</th>
                    <th scope="col">Cash In</th>
                    <th scope="col">Cash Out</th>
                    <th scope="col">Current Balance</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payable_collection as $item)
                <tr>
                    <td>{{ $item->get('account_owner') }}</td>
                    <td>{{ $item->get('account_provider') }}</td>
                    <td>{{ $item->get('year') }}</td>
                    <td>{{ $item->get('month') }}</td>
                    <td>{{ $item->get('cash_in') }}</td>
                    <td>{{ $item->get('cash_outs') }}</td>
                    <td>{{ $item->get('balance') }}</td>
                    <td>
                        <a class="btn btn-outline-info btn-sm"
                            href="{{ route('account.transactions',['account' => $item->get('account_id')]) }}">
                            <i class="fas fa-exchange-alt"></i>
                            Transactions
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
{{-- Payable Accounts --}}

@endsection

@section('pageJs')
@endsection