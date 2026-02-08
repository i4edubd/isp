@section('contentTitle')
<h3>Accounts Daily Report</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item active">Daily Report</li>
</ol>
@endsection

@section('content')

{{-- Filter --}}
<div class="card">
    <div class="card-body">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <form class="form-inline" action="{{ route('accounts.daily-report') }}" method="get">
                {{-- date --}}
                <input type="text" name="date" id="datepicker" class="form-control mr-2" placeholder="Date"
                    autocomplete="off">
                {{-- date --}}
                <button class="btn btn-outline-success" type="submit">Filter</button>
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
                    <th scope="col">Date</th>
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
                    <td>{{ $item->get('date') }}</td>
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
                    <th scope="col">Date</th>
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
                    <td>{{ $item->get('date') }}</td>
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
<script>
    $(function() {
        $('#datepicker').datepicker({
            autoclose: !0
        });
    });
</script>
@endsection