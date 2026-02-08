@section('contentTitle')
@endsection

@section('content')

<h3>By using the software, you are agreeing to be bound by the terms of this data policy.</h3>

<div class="card">

    <div class="card-body">

        <dl>

            {{-- Delete all disabled subscriptions --}}
            <dt>
                Disabled Subscriptions Delete Policy:
            </dt>
            <dd>
                <ol>
                    <li>
                        If any account's subscription status remains suspended for more than three months. The account
                        will
                        be deleted automatically.
                    </li>
                </ol>
            </dd>
            {{-- Delete all disabled subscriptions --}}

            {{-- yearly --}}
            <dt>
                If the age of the data of the following types is greater than eleven months,
                the data will be Purged for freeing up space in the database.
            </dt>
            <dd>
                <ol>
                    <li>Card Distributor Payments</li>
                    <li>Account Cash In History</li>
                    <li>Account Cash Out History</li>
                    <li>Customer Bills</li>
                    <li>Customer Payments</li>
                    <li>Customer Complaints</li>
                    <li>SMS Histories</li>
                </ol>
            </dd>
            {{-- yearly --}}

            {{-- biyearly --}}
            <dt>
                If the age of the data of the following types is greater than twenty months,
                the data will be Purged for freeing up space in the database.
            </dt>
            <dd>
                <ol>
                    <li>Expense History</li>
                    <li>Income History</li>
                    <li>SMS Payments</li>
                    <li>Subscription Payment History</li>
                </ol>
            </dd>
            {{-- biyearly --}}

            {{-- Confidential --}}
            <dt>Confidential</dt>
            <dd>None of the Parties will disclose the confidential information of Clients.</dd>
            {{-- Confidential --}}

        </dl>

    </div>

</div>

@endsection

@section('pageJs')
@endsection
