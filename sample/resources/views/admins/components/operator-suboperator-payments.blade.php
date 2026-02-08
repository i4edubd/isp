@section('contentTitle')
@endsection

@section('content')

    {{-- @Filter --}}
    <form class="d-flex align-content-start flex-wrap" action="{{ route('operator_suboperator_payments.index') }}" method="get">

        {{-- Group Admin --}}
        @if (Auth::user()->role == 'super_admin' && isset($group_admins))
            <div class="form-group col-md-2">
                <select name="operator_id" id="operator_id" class="form-control">
                    <option value=''>Admin...</option>
                    @foreach ($group_admins as $group_admin)
                        <option value="{{ $group_admin->id }}">
                            {{ $group_admin->id }} :: {{ $group_admin->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        {{-- Group Admin --}}

        {{-- type --}}
        <div class="form-group col-md-2">
            <select name="type" id="type" class="form-control">
                <option value=''>payment type...</option>
                <option value='Cash'>Cash</option>
                <option value='Online'>Online</option>
                <option value='RechargeCard'>RechargeCard</option>
            </select>
        </div>
        {{-- type --}}

        {{-- year --}}
        <div class="form-group col-md-2">
            <select name="year" id="year" class="form-control">
                <option value=''>year...</option>
                @php
                    $start = date(config('app.year_format'));
                    $stop = $start - 5;
                @endphp
                @for ($i = $start; $i >= $stop; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>
        {{-- year --}}

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
        {{-- month --}}

        <!-- date -->
        <div class="form-group col-md-2">
            <input type="text" name="date" class="form-control" id="datepicker" placeholder="date">
        </div>
        <!-- /date -->

        {{-- note --}}
        <div class="form-group col-md-2">
            <input type="text" name="note" id="note" class="form-control" placeholder="note ...">
        </div>
        {{-- note --}}

        {{-- cash_collector_id --}}
        <div class="form-group col-md-2">
            <select name="cash_collector_id" id="cash_collector_id" class="form-control">
                <option value=''>Cash Collector...</option>
                @foreach (Auth::user()->operators->where('role', 'manager')->push(Auth::user()) as $manager)
                    <option value="{{ $manager->id }}"> {{ $manager->name }} </option>
                @endforeach
            </select>
        </div>
        {{-- cash_collector_id --}}

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
        {{-- Page length --}}

        <div class="form-group col-md-1">
            <button type="submit" class="btn btn-dark">FILTER</button>
        </div>

    </form>
    {{-- @endFilter --}}

    <div class="card">

        <div class="card-body">

            {{-- Summary --}}
            <nav class="navbar justify-content-end">
                <p class="font-weight-bold mr-4">Total Amount: {{ $total_amount }}</p>

                @if (Auth::user()->show_payment_breakdown == 'yes')
                    <p class="font-weight-bold mr-4">First Party: {{ $first_party }}</p>
                    <p class="font-weight-bold mr-4">Second Party: {{ $second_party }}</p>
                    <p class="font-weight-bold mr-4">Third Party: {{ $third_party }}</p>
                @endif
            </nav>
            {{-- Summary --}}

            <table id="phpPaging" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Operator ID</th>
                        <th scope="col">Mobile / Username</th>
                        <th scope="col">Type</th>
                        <th scope="col" style="width: 30%">Amount</th>
                        <th scope="col">Payment Gateway</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($payments as $payment)
                        <tr>
                            <td scope="row">{{ $payment->id }}</td>
                            <td>{{ $payment->operator_id }}</td>
                            <td>
                                {{ $payment->mobile }} <br>
                                {{ $payment->username }}
                            </td>
                            <td>{{ $payment->type }}</td>
                            <td>
                                <div class="row">
                                    <div class="col-sm">
                                        Amount Paid: {{ $payment->amount_paid }} <br>
                                        Store Amount: {{ $payment->store_amount }} <br>
                                        Transaction Fee: {{ $payment->transaction_fee }} <br>
                                        Discount: {{ $payment->discount }} <br>
                                        VAT: {{ $payment->vat_paid }}
                                    </div>
                                    <div class="col-sm">
                                        @if (Auth::user()->show_payment_breakdown == 'yes')
                                            First Party: {{ $payment->first_party }} <br>
                                            Second Party: {{ $payment->second_party }} <br>
                                            Third Party: {{ $payment->third_party }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $payment->payment_gateway_name }}</td>
                            <td>{{ $payment->pay_status }}</td>
                            <td>{{ $payment->date }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

            {{ $payments->links() }}

        </div>

    </div>

@endsection