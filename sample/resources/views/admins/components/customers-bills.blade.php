@section('contentTitle')
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('customer_bills.index') }}" method="get">

    {{-- customer_zone_id --}}
    <div class="form-group col-md-2">
        <select name="customer_zone_id" id="customer_zone_id" class="form-control">
            <option value=''>zone...</option>
            @foreach (Auth::user()->customer_zones as $customer_zone)
            <option value="{{ $customer_zone->id }}">{{ $customer_zone->name }}</option>
            @endforeach
        </select>
    </div>
    {{--customer_zone_id --}}

    {{-- package_id --}}
    <div class="form-group col-md-2">
        <select name="package_id" id="package_id" class="form-control">
            <option value=''>package...</option>
            @foreach (Auth::user()->assigned_packages as $package)
            <option value="{{ $package->id }}">{{ $package->name }}</option>
            @endforeach
        </select>
    </div>
    {{--package_id --}}

    {{-- year --}}
    <div class="form-group col-md-2">
        <select name="year" id="year" class="form-control">
            <option value=''>year...</option>
            @php
            $start = date(config('app.year_format'));
            $stop = $start - 3;
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

    {{-- due_date --}}
    <div class="form-group col-md-2">
        <input type="text" name="due_date" id="datepicker" class="form-control" placeholder="Due Date"
            autocomplete="off">
    </div>
    {{-- due_date --}}

    {{-- Page length --}}
    <div class="form-group col-md-2">
        <select name="length" id="length" class="form-control">
            <option value="{{ $length }}" selected>Show {{ $length }} entries </option>
            <option value="10">Show 10 entries</option>
            <option value="25">Show 25 entries</option>
            <option value="50">Show 50 entries</option>
            <option value="100">Show 100 entries</option>
            <option value="200">Show 200 entries</option>
            <option value="300">Show 300 entries</option>
            <option value="500">Show 500 entries</option>
        </select>
    </div>
    {{--Page length --}}

    {{-- operator --}}
    @if (Auth::user()->role == 'group_admin' || Auth::user()->role == 'operator')
    <div class="form-group col-md-2">
        <select name="operator_id" id="operator_id" class="form-control">
            <option value=''>operator...</option>
            @foreach (Auth::user()->operators->where('role', '!=', 'manager') as $operator)
            <option value="{{ $operator->id }}">
                {{ $operator->id }} :: {{ $operator->name }} :: {{ $operator->readable_role }}
            </option>
            @endforeach
        </select>
    </div>
    @endif
    {{--operator --}}

    <div class="form-group col-md-1">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>

{{-- @endFilter --}}

{{-- Download Invoices --}}
<ul class="nav nav-pills justify-content-end">
    <li class="nav-item">
        <a class="nav-link bg-maroon" href="{{ route('customers-invoice-download.create') }}"> <i
                class="fas fa-download"></i> Download Invoices</a>
    </li>
</ul>
{{-- Download Invoices --}}

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

    {{-- modal-delete --}}
    <div class="modal" id="modal-delete" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Please Confirm</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this item?</p>
                </div>
                <div class="modal-footer">
                    <form action="" method="POST" id="delete-form">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- modal-delete --}}


    <div class="card-body">

        <h5 class="card-title">
            Total From Customers: {{ $customers_amount }} {{ config('consumer.currency') }}
            <span class="text-danger"> | </span>
            Total To upstream: {{ $operators_amount }} {{ config('consumer.currency') }}
        </h5>

        {{-- Realtime Search --}}
        <nav class="navbar justify-content-end">
            <form class="form-inline">
                {{-- mobile_serach --}}
                <input class="form-control mr-sm-2" id="mobile_serach" placeholder="Search Mobile.."
                    onchange="serachCustomerMobile(this.value)">
                {{-- mobile_serach --}}

                {{-- username_serach --}}
                <input class="form-control mr-sm-2" id="username_serach" placeholder="Search username.."
                    onchange="serachCustomerUsername(this.value)">
                {{-- username_serach --}}
            </form>
        </nav>
        {{-- Realtime Search --}}

        <div id='search_result'></div>

        <form method="POST" action="{{ route('manage-bulk-customer-bills.store') }}">
            @csrf

            <table id="phpPaging" class="table table-bordered">
                <thead>
                    <tr>
                        <th style="text-align: center;">
                            <input id="selectAll" type="checkbox">
                        </th>
                        <th scope="col">#</th>
                        <th scope="col">Customer ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">package</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Billing Period</th>
                        <th scope="col">Due Date</th>
                        <th scope="col">Purpose</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($bills as $bill )
                    <tr>
                        <td style="text-align: center;">
                            <input type="checkbox" name="bill_ids[]" value="{{ $bill->id }}" aria-label="...">
                        </td>
                        <td>{{ $bill->id }}</td>
                        <td>{{ $bill->customer_id }}</td>
                        <td>{{ $bill->username }}</td>
                        <td>
                            <a href="#" onclick="showCustomerDetails('{{ $bill->customer_id }}')">
                                {{ $bill->mobile }}
                            </a>
                        </td>
                        <td>{{ $bill->description }}</td>
                        <td>{{ $bill->amount }}</td>
                        <td>{{ $bill->billing_period }}</td>
                        <td>{{ $bill->due_date }}</td>
                        <td>{{ $bill->purpose }}</td>
                        <td>
                            @include('admins.components.actions-on-customers-bills')
                        </td>
                    </tr>
                    @endforeach

                </tbody>

            </table>


            @if ($bills->count())

            @if (Auth::user()->role == 'group_admin' || Auth::user()->role == 'operator' || Auth::user()->role ==
            'sub_operator')

            {{-- with selected --}}
            <div class="form-row align-items-center">

                {{-- options --}}
                <div class="col-auto my-4">
                    <select class="custom-select mr-sm-4" name="verb" id="inlineFormCustomSelect" required>
                        <option value="">with selected...</option>
                        @can('deleteInvoice', $bill)
                        <option value="delete">Delete</option>
                        <option value="delete_and_generate">Delete and Generate</option>
                        @endcan
                        <option value="paid">Paid</option>
                    </select>
                </div>
                {{-- options --}}

                {{-- Submit btn --}}
                <div class="col-auto my-1">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                {{-- /Submit btn --}}

            </div>
            {{-- with selected --}}

            @endif

            @endif

        </form>

    </div>


    <div class="card-footer">
        <div class="row">

            <div class="col-sm-2">
                Total Entries: {{ $bills->total() }}
            </div>

            <div class="col-sm-6">
                {{ $bills->withQueryString()->links() }}
            </div>

        </div>
    </div>
    <!--/card-footer-->

</div>

@endsection

@section('pageJs')
<script>
    $(function() {
        $('#datepicker').datepicker({
            autoclose: !0
        });
    });

    $("#selectAll").click(function(){
        $("input[type=checkbox]").prop('checked', $(this).prop('checked'));
    });

    $(document).ready(function () {

        $.ajax({
            url: "/admin/customer-mobiles"
        }).done(function (result) {
            let mobiles = jQuery.parseJSON(result);
            $("#mobile_serach").autocomplete({
                source: mobiles
            });
        });

        $.ajax({
            url: "/admin/customer-usernames"
        }).done(function (result) {
            let usernames = jQuery.parseJSON(result);
            $("#username_serach").autocomplete({
                source: usernames
            });
        });

    });

    function serachCustomerMobile(customer_mobile) {
        $("#search_result").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
        if (customer_mobile.length > 1) {
            $.ajax({
                url: "/admin/customer_bills/" + customer_mobile + "?fieldname=mobile"
            }).done(function (data) {
                $("#search_result").html(data);
            });
        } else {
            $("#search_result").html("");
        }
    }


    function serachCustomerUsername(customer_username) {
        $("#search_result").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
        if (customer_username.length > 1) {
            $.ajax({
                url: "/admin/customer_bills/" + customer_username + "?fieldname=username"
            }).done(function (data) {
                $("#search_result").html(data);
            });
        } else {
            $("#search_result").html("");
        }
    }

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

    function deleteBill(url)
    {
        $("#delete-form" ).attr( "action", url );
        $('#modal-delete').modal('show');
    }

</script>
@endsection