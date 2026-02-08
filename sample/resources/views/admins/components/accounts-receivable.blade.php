@section('contentTitle')
<dl class="row">
    <dt class="px-1">Account Owner (Will receive money):</dt>
    <dd> {{ Auth::user()->name }} </dd>
</dl>
<a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('entry-for-cash-received.create') }}">
    <i class="fas fa-plus"></i>
    Entry for cash received
</a>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Accounts</li>
    <li class="breadcrumb-item active">Accounts Receivable</li>
</ol>
@endsection

@section('content')

<div class="card">

    <!--modal-account-holder -->
    <div class="modal fade" id="modal-account-holder">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-account-holder"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ModalBody-account-holder">

                    <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>
                    <div class="text-bold pt-2">Loading...</div>
                    <div class="text-bold pt-2">Please Wait</div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /modal-account-holder -->

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Receivable From</th>
                    <th scope="col">Balance</th>
                    <th scope="col">Note</th>
                    <th scope="col" style="width: 24%"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($accounts as $account )
                <tr>
                    <th scope="row">{{ $account->id }}</th>
                    <td>
                        <a href="#" onclick="showAccountOwnerDetails('{{ $account->provider->id }}')">
                            {{ $account->provider->name }} ( {{ $account->provider->company }} )
                        </a>
                    </td>
                    <td>{{ $account->balance }} {{ config('consumer.currency') }}</td>
                    <td>{{ $account->cash_out_instruction }}</td>
                    <td>

                        {{-- cash_out --}}
                        @can('cashOut', $account)
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('accounts_receivable.cash_out.create', ['account' => $account]) }}">
                            <i class="fas fa-money-check-alt"></i>
                            Cash Out
                        </a>
                        @endcan
                        {{-- cash_out --}}

                        {{-- onlineRechage --}}
                        @can('onlineRechage', $account)
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('accounts.OnlineRechage.create', ['account' => $account]) }}">
                            <i class="fas fa-money-check-alt"></i>
                            Add Balance
                        </a>
                        @endcan
                        {{-- onlineRechage --}}

                        {{-- Exchange Money --}}
                        @can('exchange', $account)
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('accounts.exchange.create', ['account' => $account->id]) }}">
                            <i class="fas fa-exchange-alt"></i>
                            Exchange Money
                        </a>
                        @endcan
                        {{-- Exchange Money --}}

                        {{-- Transactions --}}
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('account.transactions',['account' => $account]) }}">
                            <i class="fas fa-exchange-alt"></i>
                            Transactions
                        </a>
                        {{-- Transactions --}}

                        {{-- download statement --}}
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('accounts.statement.create', ['account' => $account]) }}">
                            <i class="fas fa-download"></i>
                            Download Statement
                        </a>
                        {{-- download statement --}}

                        {{--Edit--}}
                        <a class="btn btn-outline-info btn-sm mb-2"
                            href="{{ route('accounts.edit',['account' => $account]) }}">
                            <i class="fas fa-pencil-alt"></i>
                            Edit
                        </a>
                        {{--Edit--}}

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
    function showAccountOwnerDetails(operator)
    {
        $.get( "/admin/account-holder-details/" + operator, function( data ) {
            $("#modal-title-account-holder").html("Account Holder Details");
            $('#modal-account-holder').modal('show');
            $("#ModalBody-account-holder").html(data);
        });
    }
</script>
@endsection