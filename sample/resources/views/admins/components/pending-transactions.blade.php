@section('contentTitle')
<h3>Pending Transactions</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Sender</th>
                    <th scope="col">Receiver</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Note</th>
                    <th scope="col" style="width: 24%"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($pending_transactions as $pending_transaction )
                <tr>
                    <th scope="row">{{ $pending_transaction->id }}</th>
                    <td>{{ $pending_transaction->sender->name }} , {{ $pending_transaction->sender->company }} </td>
                    <td>{{ $pending_transaction->receiver->name }} , {{ $pending_transaction->receiver->company }} </td>
                    <td>{{ $pending_transaction->amount }} {{ config('consumer.currency') }}</td>
                    <td>{{ $pending_transaction->note }}</td>
                    <td class="d-sm-flex">

                        @if (Auth::user()->id === $pending_transaction->account_owner)

                        {{-- Received --}}
                        <form method="post"
                            action="{{ route('cash_outs.store', ['pending_transaction' => $pending_transaction->id ]) }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="far fa-check-circle"></i>
                                Received
                            </button>
                        </form>
                        {{-- Received --}}

                        {{-- Not Received --}}
                        <form method="post"
                            action="{{ route('pending_transactions.destroy',['pending_transaction' => $pending_transaction->id ]) }}">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="far fa-times-circle"></i>
                                Not Received
                            </button>
                        </form>
                        {{-- Not Received --}}

                        @endif

                    </td>
                </tr>
                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
