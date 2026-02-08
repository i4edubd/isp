@section('contentTitle')
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('offline_customers.index') }}" method="get">

    {{-- connection_type --}}
    <div class="form-group col-md-2">
        <select name="connection_type" id="connection_type" class="form-control">
            <option value=''>connection type...</option>
            <option value='PPPoE'>PPP</option>
            <option value='Hotspot'>Hotspot</option>
        </select>
    </div>
    {{--connection_type --}}

    {{-- status --}}
    <div class="form-group col-md-2">
        <select name="status" id="status" class="form-control">
            <option value=''>status...</option>
            <option value='active'>active</option>
            <option value='suspended'>suspended</option>
            <option value='disabled'>disabled</option>
        </select>
    </div>
    {{--status --}}

    {{-- payment_status --}}
    <div class="form-group col-md-2">
        <select name="payment_status" id="payment_status" class="form-control">
            <option value=''>payment status...</option>
            <option value='billed'>billed</option>
            <option value='paid'>paid</option>
        </select>
    </div>
    {{--payment_status --}}

    {{-- zone_id --}}
    <div class="form-group col-md-2">
        <select name="zone_id" id="zone_id" class="form-control">
            <option value=''>zone...</option>
            @foreach (Auth::user()->customer_zones as $customer_zone)
            <option value="{{ $customer_zone->id }}">{{ $customer_zone->name }}</option>
            @endforeach
        </select>
    </div>
    {{--zone_id --}}

    {{-- device_id --}}
    <div class="form-group col-md-2">
        <select name="device_id" id="device_id" class="form-control">
            <option value=''>device...</option>
            @foreach (Auth::user()->devices->sortBy('name') as $device)
            <option value="{{ $device->id }}">{{ $device->name }} ({{ $device->location }})</option>
            @endforeach
        </select>
    </div>
    {{--device_id --}}

    {{-- package_id --}}
    <div class="form-group col-md-2">
        <select name="package_id" id="package_id" class="form-control">
            <option value=''>package...</option>
            @foreach (Auth::user()->assigned_packages->sortBy('name') as $package)
            <option value="{{ $package->id }}">{{ $package->name }}</option>
            @endforeach
        </select>
    </div>
    {{--package_id --}}

    {{-- sortby --}}
    <div class="form-group col-md-2">
        <select name="sortby" id="sortby" class="form-control">
            <option value=''>Sort By...</option>
            <option value="id">Customer ID</option>
            <option value="username">Username</option>
            <option value="last_seen_timestamp">Last Seen</option>
        </select>
    </div>
    {{-- sortby --}}

    {{-- username --}}
    <div class="form-group col-md-2">
        <input type="text" name="username" id="username" class="form-control" placeholder="username LIKE ...">
    </div>
    {{-- username --}}

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

    {{-- operator --}}
    @if (Auth::user()->role == 'group_admin')
    <div class="form-group col-md-2">
        <select name="operator_id" id="operator_id" class="form-control">
            <option value=''>operator...</option>
            @foreach (Auth::user()->operators->where('role', '!=', 'manager') as $operator)
            <option value="{{ $operator->id }}"> {{ $operator->name }} </option>
            @endforeach
        </select>
    </div>
    @endif
    {{--operator --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>

{{-- @endFilter --}}

<div class="card">

    <!--modal -->
    <div class="modal fade" id="modal-customer">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ModalBody">

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
    <!-- /modal -->

    <div class="card-body">

        <form action="{{ route('multiple-customer-update') }}" method="POST"
            onsubmit="return confirm('Are You sure? You want to delete all selected customers?')">
            @csrf

            <table id="phpPaging" class="table table-bordered">

                <thead>
                    <tr>
                        <th style="text-align: center;">
                            <input id="selectAll" type="checkbox">
                        </th>
                        <th>#</th>
                        <th>Mobile/Name</th>
                        <th>Username/Last Seen</th>
                        <th>package/Validity</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($customers as $customer )

                    <tr class="{{ $customer->color }}">

                        <td style="text-align: center;">
                            <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}">
                        </td>

                        <td scope="row">{{ $customer->id }}</td>

                        <td>
                            <a href="#" onclick="showCustomerDetails('{{ $customer->id }}')">
                                {{ $customer->mobile }}
                            </a>
                            <br>
                            {{ $customer->name }}
                        </td>

                        <td>
                            {{ $customer->username }}
                            <br>
                            {{ $customer->last_seen }}
                        </td>

                        <td>
                            {{ $customer->package_name }}
                            <br>
                            {{ $customer->package_expired_at }}
                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

            {{-- with selected --}}
            <div class="form-row align-items-center">

                {{-- options --}}
                <div class="col-auto my-4">
                    <select class="form-control" name="verb" id="inlineFormCustomSelect" required>
                        <option value="">with selected...</option>
                        @if (Auth::user()->role == 'group_admin')
                        <option value="delete">Delete</option>
                        @endif
                    </select>
                </div>
                {{-- options --}}

                {{-- Submit btn --}}
                <div class="col-auto my-1">
                    <button type="submit" class="btn btn-primary" id="btn-submit">Submit</button>
                </div>
                {{-- /Submit btn --}}

            </div>
            {{-- with selected --}}

        </form>

    </div>

    <div class="card-footer">
        <div class="row">

            <div class="col-sm-2">
                Total Entries: {{ $customers->total() }}
            </div>

            <div class="col-sm-6">
                {{ $customers->withQueryString()->links() }}
            </div>

        </div>
    </div>
    <!--/card-footer-->

</div>

@endsection

@section('pageJs')

<script>
    function showCustomerDetails(customer)
    {
        $("#modal-title").html("Customer Details");
        $("#ModalBody").html('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>');
        $("#ModalBody").append('<div class="text-bold pt-2">Loading...</div>');
        $("#ModalBody").append('<div class="text-bold pt-2">Please Wait</div>');
        $('#modal-customer').modal('show');
        $.get( "/admin/customer-details/" + customer, function( data ) {
            $("#ModalBody").html(data);
        });
    }

    $("#selectAll").click(function(){
        $("input[type=checkbox]").prop('checked', $(this).prop('checked'));
    });

</script>

@endsection
