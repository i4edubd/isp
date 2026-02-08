@section('contentTitle')
@include('admins.components.account-title')
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item"><a href="{{ $previous_url }}"> {{ $breadcrumb_label }}</a></li>
    <li class="breadcrumb-item active">Transactions</li>
</ol>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        {{-- Filter --}}
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <form class="form-inline my-2 my-lg-0"
                action="{{ route('account.transactions',['account' => $account->id]) }}" method="get">
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
        {{-- Filter --}}

        {{-- header --}}
        <div class="d-flex p-0">
            <ul class="nav nav-pills ml-auto p-2">
                <li class="nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Cash Ins</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Cash Outs</a></li>
                <li class="nav-item"><a class="nav-link" href="#tab_3" data-toggle="tab">Summary</a></li>
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('accounts.statement.create', ['account' => $account->id]) }}">Download
                        Statement</a>
                </li>
            </ul>
        </div>
        {{-- header --}}

        <div class="tab-content">

            {{-- tab_1 --}}
            <div class="tab-pane active" id="tab_1">
                @php
                $in = $transaction_report['in'];
                @endphp
                <table class="table table-bordered table-striped" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Total In</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1; $i<=12; $i++) @php $month=date_format(date_create(date('01-' . $i . '-Y' )),
                            config('app.month_format')); @endphp @foreach ( $in[$month] as $mr ) <tr>
                            <td>{{ $mr->year }}</td>
                            <td>{{ $mr->month }}</td>
                            <td>{{ $mr->amount }}</td>
                            <td>
                                <a
                                    href="{{ route('account.ins', ['account' => $account->id, 'year' => $mr->year, 'month' => $mr->month]) }}">
                                    <i class="fas fa-expand-alt"></i>
                                    Details
                                </a>
                            </td>
                            </tr>
                            @endforeach
                            @endfor
                    </tbody>
                </table>
            </div>
            {{-- tab_1 --}}
            {{-- tab_2 --}}
            <div class="tab-pane" id="tab_2">
                @php
                $out = $transaction_report['out'];
                @endphp
                <table class="table table-bordered table-striped" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Total Out</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1; $i<=12; $i++) @php $month=date_format(date_create(date('01-' . $i . '-Y' )),
                            config('app.month_format')); @endphp @foreach ( $out[$month] as $mr ) <tr>
                            <td>{{ $mr->year }}</td>
                            <td>{{ $mr->month }}</td>
                            <td>{{ $mr->amount }}</td>
                            <td>
                                <a
                                    href="{{ route('account.outs', ['account' => $account->id, 'year' => $mr->year, 'month' => $mr->month]) }}">
                                    <i class="fas fa-expand-alt"></i>
                                    Details
                                </a>
                            </td>
                            </tr>
                            @endforeach
                            @endfor
                    </tbody>
                </table>
            </div>
            {{-- tab_2 --}}
            {{-- tab_3 --}}
            <div class="tab-pane" id="tab_3">
                <table class="table table-bordered table-striped" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Month</th>
                            <th>In</th>
                            <th>Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i=1; $i<=12; $i++) @php $month=date_format(date_create(date('01-' . $i . '-Y' )),
                            config('app.month_format')); @endphp <tr>
                            <td>{{ $year }}</td>
                            <td>{{ $month }}</td>
                            <td>
                                {{ $summary_ins->where('year', $year)->where('month', $month)->first() ?
                                $summary_ins->where('year', $year)->where('month', $month)->first()->amount : 0 }}
                            </td>
                            <td>
                                {{ $summary_outs->where('year', $year)->where('month', $month)->first() ?
                                $summary_outs->where('year', $year)->where('month', $month)->first()->amount : 0 }}
                            </td>
                            </tr>
                            @endfor
                    </tbody>
                </table>
            </div>
            {{-- tab_3 --}}
        </div>
        <!-- /tab-content -->

    </div>

</div>

@endsection

@section('pageJs')
@endsection