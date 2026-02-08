@section('contentTitle')
@endsection

@section('content')

<div class="d-flex flex-wrap">
    <h3 class="mr-4">Billing Summary</h3>
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modal-secondary">
        (Re)Generate Reports
    </button>

    <form class="form-inline ml-4" method="POST" action="{{ route('customer_bills_summary_download.store') }}">
        @csrf
        <button type="submit" class="btn btn-primary">Download Report</button>
    </form>

</div>

<div class="modal fade" id="modal-secondary">
    <div class="modal-dialog">
        <div class="modal-content bg-secondary">
            <div class="modal-header">
                <h4 class="modal-title">Please Confirm</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Previously generated report will be deleted and a new report will generate.</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-light" data-dismiss="modal">Close</button>
                <a class="btn btn-outline-light" href="{{ route('customer_bills_summary.create') }}"
                    role="button">Generate Report</a>
            </div>
        </div>
        <!-- /modal-content -->
    </div>
    <!-- /modal-dialog -->
</div>
<!-- /modal -->

{{-- Direct Selling --}}
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">Business From Direct Selling</h3>
    </div>
    <!-- /card-header -->
    <div class="card-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Package</th>
                    <th scope="col">Bill Count</th>
                    <th scope="col">Package Price</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills_summaries->where('type', 'direct') as $DirectSelling)
                <tr>
                    <th>{{ $DirectSelling->package->name }}</th>
                    <td>{{ $DirectSelling->bill_count }}</td>
                    <td>{{ $DirectSelling->package_price }}</td>
                    <td>{{ $DirectSelling->subtotal }}</td>
                </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <th></th>
                    <td></td>
                    <td>Total: </td>
                    <td>{{ $bills_summaries->where('type', 'direct')->sum('subtotal') }}</td>
                </tr>
            </tbody>
        </table>

    </div>
    <!-- /card-body -->
</div>
{{-- Direct Selling --}}

{{-- Business From Resellers --}}

@if (Auth::user()->role == 'group_admin' || Auth::user()->role == 'operator')

<div class="card card-outline card-primary">

    <div class="card-header">
        <h3 class="card-title">Business From Resellers</h3>
    </div>
    <!-- /card-header -->

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Resellers</th>
                    <th scope="col">Package</th>
                    <th scope="col">Bill Count</th>
                    <th scope="col">Package Price</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills_summaries->where('type', 'resell')->groupBy('reseller_id') as $FromResellers)
                @foreach ($FromResellers as $FromReseller)
                <tr>
                    <th>{{ $FromReseller->reseller->id }} :: {{ $FromReseller->reseller->name }}</th>
                    <th>{{ $FromReseller->package->name }}</th>
                    <td>{{ $FromReseller->bill_count }}</td>
                    <td>{{ $FromReseller->package_price }}</td>
                    <td>{{ $FromReseller->subtotal }}</td>
                </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <th></th>
                    <td></td>
                    <td></td>
                    <td>Total: </td>
                    <td>{{ $FromResellers->sum('subtotal') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- /card-body -->

</div>

@endif

{{-- Business From Resellers --}}

{{-- Business From Sub-Resellers --}}

@if (Auth::user()->role == 'group_admin')

<div class="card card-outline card-secondary">

    <div class="card-header">
        <h3 class="card-title">Business From Sub-Resellers (Resellers of Resellers)</h3>
    </div>
    <!-- /card-header -->

    <div class="card-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Resellers</th>
                    <th scope="col">Package</th>
                    <th scope="col">Bill Count</th>
                    <th scope="col">Package Price</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills_summaries->where('type', 'sub_resell')->groupBy('reseller_id') as $FromSubResellers)
                @foreach ($FromSubResellers as $FromSubReseller)
                <tr>
                    <th>
                        {{ $FromSubReseller->reseller->id }} :: {{ $FromSubReseller->reseller->name }}
                        <br>
                        ({{ $FromSubReseller->sub_reseller->id }} :: {{ $FromSubReseller->sub_reseller->name }})
                    </th>
                    <th>{{ $FromSubReseller->package->name }}</th>
                    <td>{{ $FromSubReseller->bill_count }}</td>
                    <td>{{ $FromSubReseller->package_price }}</td>
                    <td>{{ $FromSubReseller->subtotal }}</td>
                </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <th></th>
                    <td></td>
                    <td></td>
                    <td>Total: </td>
                    <td>{{ $FromSubResellers->sum('subtotal') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    <!-- /card-body -->

</div>

@endif
{{-- Business From Sub-Resellers --}}


{{-- to_operator --}}

@if (Auth::user()->role == 'sub_operator')

<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title">Payable To: {{ Auth::user()->group_admin->name }} </h3>
    </div>
    <!-- /card-header -->
    <div class="card-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Package</th>
                    <th scope="col">Bill Count</th>
                    <th scope="col">Package Price</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills_summaries->where('type', 'to_operator') as $to_operator)
                <tr>
                    <th>{{ $to_operator->package->name }}</th>
                    <td>{{ $to_operator->bill_count }}</td>
                    <td>{{ $to_operator->package_price }}</td>
                    <td>{{ $to_operator->subtotal }}</td>
                </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <th></th>
                    <td></td>
                    <td>Total: </td>
                    <td>{{ $bills_summaries->where('type', 'to_operator')->sum('subtotal') }}</td>
                </tr>
            </tbody>
        </table>

    </div>
    <!-- /card-body -->
</div>

@endif
{{-- to_operator --}}

{{-- to_group_admin --}}
@if (Auth::user()->role == 'operator')

<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title">Payable To: {{ Auth::user()->group_admin->name }} </h3>
    </div>
    <!-- /card-header -->
    <div class="card-body">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">Package</th>
                    <th scope="col">Bill Count</th>
                    <th scope="col">Package Price</th>
                    <th scope="col">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($bills_summaries->where('type', 'to_group_admin') as $to_group_admin)
                <tr>
                    <th>{{ $to_group_admin->package->name }}</th>
                    <td>{{ $to_group_admin->bill_count }}</td>
                    <td>{{ $to_group_admin->package_price }}</td>
                    <td>{{ $to_group_admin->subtotal }}</td>
                </tr>
                @endforeach
                <tr class="font-weight-bold">
                    <th></th>
                    <td></td>
                    <td>Total: </td>
                    <td>{{ $bills_summaries->where('type', 'to_group_admin')->sum('subtotal') }}</td>
                </tr>
            </tbody>
        </table>

    </div>
    <!-- /card-body -->
</div>

@endif
{{-- to_group_admin --}}

{{-- Note --}}
<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title">Note</h3>
    </div>
    <!-- /card-header -->
    <div class="card-body">
        Summary bills has been calculated as follows: <br>
        Subtotal = (Bill Count) x (Package Price)
        <hr>
        If there are fractional bills, the calculated amount could differ with actual amount.
    </div>
    <!-- /card-body -->
</div>
{{-- Note --}}

@endsection

@section('pageJs')
@endsection
