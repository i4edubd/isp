@section('contentTitle')
@endsection

@section('content')

    {{-- @Filter --}}
    <form class="d-flex align-content-start flex-wrap" action="{{ route('customers.index') }}" method="get">

        
        {{-- billing_type --}}
        @if (array_key_exists('billing_type', $filters))
            <div class="form-group col-md-2">
                <select name="billing_type" id="billing_type" class="form-control">
                    <option value=''>billing type...</option>
                    <option value='Daily'>Daily</option>
                    <option value='Monthly'>Monthly</option>
                    <option value='Free'>Free</option>
                </select>
            </div>
        @endif
        {{-- billing_type --}}

        {{-- status --}}
        @if (array_key_exists('status', $filters))
            <div class="form-group col-md-2">
                <select name="status" id="status" class="form-control">
                    <option value=''>status...</option>
                    <option value='active'>active</option>
                    <option value='suspended'>suspended</option>
                    <option value='disabled'>disabled</option>
                </select>
            </div>
        @endif
        {{-- status --}}

	{{-- username --}}
    	<div class="form-group col-md-2">
        <input type="text" name="username" id="username" class="form-control" placeholder="username LIKE ...">
    	</div>
    	{{-- username --}}


        {{-- payment_status --}}
        @if (array_key_exists('payment_status', $filters))
            <div class="form-group col-md-2">
                <select name="payment_status" id="payment_status" class="form-control">
                    <option value=''>payment status...</option>
                    <option value='billed'>billed</option>
                    <option value='paid'>paid</option>
                </select>
            </div>
        @endif
        {{-- payment_status --}}

        
        {{-- package_id --}}
        @if (array_key_exists('package_id', $filters))
            <div class="form-group col-md-2">
                <select name="package_id" id="package_id" class="form-control">
                    <option value=''>package...</option>
                    @foreach ($all_packages->groupBy('operator_id') as $gpackages)
                        @foreach ($gpackages->sortBy('name') as $package)
                            <option value="{{ $package->id }}">{{ $package->operator->id }} ::
                                {{ $package->operator->name }} :: {{ $package->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
        @endif
        {{-- package_id --}}

        {{-- billing_profile_id --}}
        @if (array_key_exists('billing_profile_id', $filters))
            <div class="form-group col-md-2">
                <select name="billing_profile_id" id="billing_profile_id" class="form-control">
                    <option value=''>Billing Profile...</option>
                    @foreach ($billing_profiles->sortBy('name') as $billing_profile)
                        <option value="{{ $billing_profile->id }}">{{ $billing_profile->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        {{-- billing_profile_id --}}

        
        {{-- mac_bind --}}
        @if (array_key_exists('mac_bind', $filters))
            <div class="form-group col-md-2">
                <select name="mac_bind" id="mac_bind" class="form-control">
                    <option value=''>mac bind...</option>
                    <option value='0'>False</option>
                    <option value='1'>True</option>
                </select>
            </div>
        @endif
        {{-- mac_bind --}}

        {{-- advance_payment --}}
        @if (array_key_exists('advance_payment', $filters))
            <div class="form-group col-md-2">
                <select name="advance_payment" id="advance_payment" class="form-control">
                    <option value=''>advance payment...</option>
                    <option value='0'>No</option>
                    <option value='1'>Yes</option>
                </select>
            </div>
        @endif
        {{-- advance_payment --}}

        {{-- year --}}
        @if (array_key_exists('year', $filters))
            <div class="form-group col-md-2">
                <select name="year" id="year" class="form-control">
                    <option value=''>Reg. year...</option>
                    @php
                        $start = date(config('app.year_format'));
                        $stop = $start - 5;
                    @endphp
                    @for ($i = $start; $i >= $stop; $i--)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        @endif
        {{-- year --}}

        {{-- month --}}
        @if (array_key_exists('month', $filters))
            <div class="form-group col-md-2">
                <select name="month" id="month" class="form-control">
                    <option value=''>Reg. month...</option>
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
        @endif
        {{-- month --}}

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
                <option value="400">Show 400 entries</option>
                <option value="500">Show 500 entries</option>
            </select>
        </div>
        {{-- Page length --}}

        {{-- sortby --}}
        <div class="form-group col-md-2">
            <select name="sortby" id="sortby" class="form-control">
                <option value=''>Sort By...</option>
                <option value="id">Customer ID</option>
                <option value="username">Username</option>
                <option value="exptimestamp">Expiration Time</option>
            </select>
        </div>
        {{-- sortby --}}

        
        {{-- operator --}}
        @if (array_key_exists('operator_id', $filters))
            @if (Auth::user()->role == 'group_admin' || Auth::user()->role == 'operator')
                <div class="form-group col-md-2">
                    <select name="operator_id" id="operator_id" class="form-control">
                        <option value=''>operator...</option>
                        @foreach ($operators->where('role', '!=', 'manager')->sortBy('role') as $operator)
                            <option value="{{ $operator->id }}">
                                {{ $operator->id }} :: {{ $operator->name }} :: {{ $operator->role_alias }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        @endif
        {{-- operator --}}

        <div class="form-group col-md-2">
            <button type="submit" class="btn btn-dark">
                FILTER
            </button>
            <a href="#" class="ml-2 btn btn-outline-info" data-toggle="tooltip" data-placement="bottom"
                title="Customize Filter Options"
                onclick="showFilterCustomizeOption('{{ route('disabled_filters.create', ['model' => 'customer']) }}')">
                <i class="fas fa-cog"></i>
            </a>
        </div>

    </form>

    {{-- @endFilter --}}

    <div class="row">
    {{-- Online Customers --}}
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box border border-info" onclick="window.open('{{ route('online_customers.index') }}', '_blank')">
            <span class="info-box-icon bg-info"><i class="fas fa-globe"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Online Customers</span>
            </div>
        </div>
    </div>
    {{-- Offline Customers --}}
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box border border-primary" onclick="window.open('{{ route('offline_customers.index') }}', '_blank')">
            <span class="info-box-icon bg-primary"><i class="fas fa-user-times"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Offline Customers</span>
            </div>
        </div>
    </div>
    {{-- PPPoE Customers --}}
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box border border-success" onclick="window.open('{{ route('customers.index', ['connection_type' => 'PPPoE']) }}', '_blank')">
            <span class="info-box-icon bg-success"><i class="fas fa-network-wired"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">PPPoE Customers</span>
            </div>
        </div>
    </div>
    {{-- Hotspot Customers --}}
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box border border-warning" onclick="window.open('{{ route('customers.index', ['connection_type' => 'Hotspot']) }}', '_blank')">
            <span class="info-box-icon bg-warning"><i class="fas fa-wifi"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Hotspot Customers</span>
            </div>
        </div>
    </div>
    </div>

    {{-- Download & Upload --}}

    <ul class="nav justify-content-end">

        <li class="nav-item">
            @if (url()->current() == url()->full())
                <a class="nav-link text-danger" href="{{ url()->full() . '?refresh=1' }}">
                    <i class="fas fa-retweet"></i> Refresh
                </a>
            @else
                <a class="nav-link text-danger" href="{{ url()->full() . '&refresh=1' }}">
                    <i class="fas fa-retweet"></i> Refresh
                </a>
            @endif
        </li>

        @if (Auth::user()->role == 'group_admin' || Auth::user()->role == 'operator')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('download-users-downloadable.create') }}">
                    <i class="fas fa-download"></i> Download users
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('bulk-update-users.create') }}">
                    <i class="fas fa-upload"></i> Bulk update users
                </a>
            </li>
        @endif

    </ul>

    {{-- Download & Upload --}}

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

        <!-- Modal Activate Options -->
        <div class="modal fade" id="ActivateOptionsModalCenter" tabindex="-1" role="dialog"
            aria-labelledby="ActivateOptionsModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ActivateOptionsModalCenterTitle">Activate Options</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="ActivateOptionsModelContent">
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Activate Options -->

        <div class="card-body">

            {{-- Realtime Search --}}
            <nav class="navbar justify-content-end">
                <form class="form-inline">
                    {{-- id_serach --}}
                    <input class="form-control mr-sm-2" id="id_serach" type="search" placeholder="Customer ID"
                        onblur="serachCustomerId(this.value)">
                    {{-- id_serach --}}

                    {{-- mobile_serach --}}
                    <input class="form-control mr-sm-2" id="mobile_serach" type="search" placeholder="Search Mobile.."
                        onchange="serachCustomerMobile(this.value)">
                    {{-- mobile_serach --}}

                    {{-- username_serach --}}
                    <input class="form-control mr-sm-2" id="username_serach" type="search"
                        placeholder="Search username.." onchange="serachCustomerUsername(this.value)">
                    {{-- username_serach --}}

                    {{-- name_serach --}}
                    <input class="form-control mr-sm-2" id="name_serach" type="search" placeholder="Search Full Name.."
                        onchange="serachCustomerName(this.value)">
                    {{-- name_serach --}}

                </form>
            </nav>
            {{-- Realtime Search --}}

            <div id='search_result'></div>

            <form action="{{ route('multiple-customer-update') }}" method="POST">
                @csrf

                <table id="phpPaging" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center;">
                                <input id="selectAll" type="checkbox">
                            </th>
                            <th scope="col">#</th>
                            <th scope="col">Mobile &amp; <br> Name</th>
                            <th scope="col">Username &amp; <br> Password</th>
                            <th scope="col">package &amp; <br> Validity</th>
                            <th scope="col">Payment Status &amp; <br> Status </th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($customers as $customer)
                            <tr id="row-{{ $customer->id }}">
                                <td style="text-align: center;">
                                    <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}">
                                </td>

                                <td scope="row">
                                    {{ $customer->id }}
                                    <br>
                                    @if ($customer->is_online)
                                        <i class="fas fa-circle text-success"></i>
                                    @else
                                        <i class="fas fa-circle text-danger"></i>
                                    @endif
                                </td>

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
                                    {{ $customer->password }}
                                </td>
                                <td>
                                    {{ $customer->package_name }}
                                    <br>
                                    {{ $customer->package_expired_at }}
                                    <br>
                                    {{ $customer->remaining_validity }}
                                </td>
                                <td>
                                    <span class="{{ $customer->payment_color }}"> {{ $customer->payment_status }} </span>
                                    <br>
                                    <span class="{{ $customer->color }}"> {{ $customer->status }} </span>

                                </td>
                                <td>
                                    @include('admins.components.actions-on-customers')
                                </td>
                            </tr>
                        @endforeach

                    </tbody>

                </table>

                {{-- with selected --}}
                <div class="form-row align-items-center">

                    {{-- options --}}
                    <div class="col-auto my-4">
                        <select class="form-control" name="verb" id="inlineFormCustomSelect"
                            onchange="selectOption(this.value)" required>
                            <option value="">with selected...</option>
                            <option value="activate">Activate</option>
                            <option value="suspend">Suspend</option>
                            <option value="disable">Disable</option>
                            <option value="edit_zone">Edit Zone</option>
                            <option value="pay_bills">Pay Bills</option>
                            <option value="remove_mac_bind">Remove MAC Bind</option>
                            <option value="send_sms">Send SMS</option>
                            @if (Auth::user()->role !== 'manager')
                                <option value="extend_package_validity">
                                    Recharge (PPP Daily Billing OR Hotspot)
                                </option>
                            @endif
                            @if (Auth::user()->role == 'group_admin')
                                <option value="delete">Delete</option>
                                <option value="activate">Activate</option>
                                <option value="change_operator">Change Operator</option>
                                <option value="change_package">Change Package (Without Accounting)</option>
                                <option value="change_exp_date">Edit Suspend Date (Without Accounting)</option>
                                <option value="change_billing_profile">Change Billing Profile (Without Accounting)</option>
                                <option value="generate_bill">Generate Bill</option>
                            @endif
                        </select>
                    </div>
                    {{-- options --}}

                    @if (Auth::user()->role !== 'manager')
                        <div class="col-auto my-4" id="select_validity">
                            <div class="input-group">
                                <input type="number" name='validity' id='validity' class='form-control'
                                    value="1" min="1" autocomplete="off">
                                <div class="input-group-append">
                                    <span class="input-group-text">Days</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (Auth::user()->role == 'group_admin')
                        {{-- operator --}}
                        <div class="col-auto my-4" id="select_operator_option">
                            <select name="operator_id" id="new_operator_id" class="form-control">
                                <option value=''>operator...</option>
                                @foreach ($operators->where('role', '!=', 'manager') as $operator)
                                    <option value="{{ $operator->id }}"> {{ $operator->name }} </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- operator --}}

                        {{-- Package --}}
                        <div class="col-auto my-4" id="select_package_option">
                            <select name="package_id" id="new_package_id" class="form-control">
                                <option value=''>package...</option>
                                @foreach ($all_packages->groupBy('operator_id') as $gpackages)
                                    @foreach ($gpackages->sortBy('name') as $package)
                                        <option value="{{ $package->id }}">{{ $package->operator->id }} ::
                                            {{ $package->operator->name }} :: {{ $package->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        {{-- Package --}}

                        <!--change_exp_date-->
                        <div class="col-auto my-4" id="select_exp_date">
                            <input type='text' name='new_exp_date' id='new_exp_date' class='form-control'
                                placeholder="Suspend Date" autocomplete="off">
                        </div>
                        <!--/change_exp_date-->

                        {{-- billing_profile_id --}}
                        <div class="col-auto my-4" id="select_billing_profile_option">
                            <select name="billing_profile_id" id="new_billing_profile_id" class="form-control">
                                <option value=''>Billing Profile...</option>
                                @foreach ($billing_profiles as $billing_profile)
                                    <option value="{{ $billing_profile->id }}">{{ $billing_profile->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- billing_profile_id --}}
                    @endif

                    {{-- zone_id --}}
                    <div class="col-auto my-4" id="select_zone_option">
                        <select name="zone_id" id="new_zone_id" class="form-control">
                            <option value=''>Zone...</option>
                            @foreach ($zones as $customer_zone)
                                <option value="{{ $customer_zone->id }}">{{ $customer_zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- zone_id --}}

                    <!--message-->
                    <div class="col-auto my-4" id="type_sms_body">
                        <label for="sms_body">Message</label>
                        <textarea class="form-control" id="sms_body" name="message" rows="3"></textarea>
                    </div>
                    <!--message-->

                    {{-- Submit btn --}}
                    <div class="col-auto my-1">
                        <button type="submit" class="btn btn-primary" id="btn-submit"
                            disabled="disabled">Submit</button>
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
                    {{ $customers->links() }}
                </div>

            </div>
        </div>
        <!--/card-footer-->

    </div>

@endsection

@section('pageJs')
    <script>
        $(document).ready(function() {

            $("#select_operator_option").hide();
            $("#select_package_option").hide();
            $("#select_billing_profile_option").hide();
            $("#select_exp_date").hide();
            $("#select_validity").hide();
            $("#select_zone_option").hide();
            $("#type_sms_body").hide();

            //Initialize Select2 Elements
            $('.select2').select2();

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });

            $('[data-mask]').inputmask();

            $(function() {
                $('#new_exp_date').datepicker({
                    autoclose: !0
                });
            });

            $.ajax({
                url: "/admin/customer-mobiles"
            }).done(function(result) {
                let mobiles = jQuery.parseJSON(result);
                $("#mobile_serach").autocomplete({
                    source: mobiles,
                    autoFocus: true,
                    select: function(event, ui) {
                        var value = ui.item.value;
                        $("#mobile_serach").val(value);
                        $("#mobile_serach").blur();
                    }
                });
            });

            $.ajax({
                url: "/admin/customer-usernames"
            }).done(function(result) {
                let usernames = jQuery.parseJSON(result);
                $("#username_serach").autocomplete({
                    source: usernames,
                    autoFocus: true,
                    select: function(event, ui) {
                        var value = ui.item.value;
                        $("#username_serach").val(value);
                        $("#username_serach").blur();
                    }
                });
            });

            $.ajax({
                url: "/admin/customer-names"
            }).done(function(result) {
                let names = jQuery.parseJSON(result);
                $("#name_serach").autocomplete({
                    source: names,
                    autoFocus: true,
                    select: function(event, ui) {
                        var value = ui.item.value;
                        $("#name_serach").val(value);
                        $("#name_serach").blur();
                    }
                });
            });

        });

        $("#selectAll").click(function() {
            $("input[type=checkbox]").prop('checked', $(this).prop('checked'));
        });

        function serachCustomerId(customer_id) {
            $("#search_result").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            if (customer_id.length > 1) {
                $.ajax({
                    url: "/admin/customer-id/" + customer_id
                }).done(function(data) {
                    $("#search_result").html(data);
                });
            } else {
                $("#search_result").html("");
            }
        }

        function serachCustomerMobile(customer_mobile) {
            $("#search_result").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            if (customer_mobile.length > 1) {
                $.ajax({
                    url: "/admin/customer-mobiles/" + customer_mobile
                }).done(function(data) {
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
                    url: "/admin/customer-usernames/" + customer_username
                }).done(function(data) {
                    $("#search_result").html(data);
                });
            } else {
                $("#search_result").html("");
            }
        }

        function serachCustomerName(customer_name) {
            $("#search_result").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            if (customer_name.length > 1) {
                $.ajax({
                    url: "/admin/customer-names/" + customer_name
                }).done(function(data) {
                    $("#search_result").html(data);
                });
            } else {
                $("#search_result").html("");
            }
        }

        function showCustomerDetails(customer) {
            $("#modal-title").html("Customer Details");
            $("#ModalBody").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Loading...</div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Please Wait</div>');
            $('#modal-customer').modal('show');
            $.get("/admin/customer-details/" + customer, function(data) {
                $("#ModalBody").html(data);
            });
        }

        function showFilterCustomizeOption(url) {
            $("#modal-title").html("Turn ON/OFF Filter Options");
            $("#ModalBody").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Loading...</div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Please Wait</div>');
            $('#modal-customer').modal('show');
            $.get(url, function(data) {
                $("#ModalBody").html(data);
            });
        }

        function deleteCustomer(url) {
            $("#delete-form").attr("action", url);
            $('#modal-delete').modal('show');
        }

        function editIP(url) {
            $("#modal-title").html("Edit IP Address");
            $("#ModalBody").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Loading...</div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Please Wait</div>');
            $('#modal-customer').modal('show');
            $.get(url, function(data) {
                $("#ModalBody").html(data);
            });
        }

        function selectOption(value) {
            switch (value) {
                case 'change_operator':
                    document.getElementById("new_operator_id").required = true;
                    document.getElementById("new_package_id").required = false;
                    document.getElementById("new_exp_date").required = false;
                    document.getElementById("new_billing_profile_id").required = false;
                    document.getElementById("validity").required = false;
                    document.getElementById("new_zone_id").required = false;
                    document.getElementById("sms_body").required = false;
                    $("#select_package_option").hide();
                    $("#select_exp_date").hide();
                    $("#select_billing_profile_option").hide();
                    $("#select_operator_option").show();
                    $("#select_validity").hide();
                    $("#select_zone_option").hide();
                    $("#type_sms_body").hide();
                    $("#btn-submit").attr("disabled", false);
                    break;
                case 'change_package':
                    document.getElementById("new_operator_id").required = false;
                    document.getElementById("new_exp_date").required = false;
                    document.getElementById("new_billing_profile_id").required = false;
                    document.getElementById("new_package_id").required = true;
                    document.getElementById("validity").required = false;
                    document.getElementById("new_zone_id").required = false;
                    document.getElementById("sms_body").required = false;
                    $("#select_package_option").show();
                    $("#select_operator_option").hide();
                    $("#select_exp_date").hide();
                    $("#select_billing_profile_option").hide();
                    $("#select_validity").hide();
                    $("#select_zone_option").hide();
                    $("#type_sms_body").hide();
                    $("#btn-submit").attr("disabled", false);
                    break;
                case 'change_billing_profile':
                    document.getElementById("new_billing_profile_id").required = true;
                    document.getElementById("new_exp_date").required = false;
                    document.getElementById("new_operator_id").required = false;
                    document.getElementById("new_package_id").required = false;
                    document.getElementById("validity").required = false;
                    document.getElementById("new_zone_id").required = false;
                    document.getElementById("sms_body").required = false;
                    $("#select_package_option").hide();
                    $("#select_operator_option").hide();
                    $("#select_exp_date").hide();
                    $("#select_billing_profile_option").show();
                    $("#select_validity").hide();
                    $("#select_zone_option").hide();
                    $("#type_sms_body").hide();
                    $("#btn-submit").attr("disabled", false);
                    break;
                case 'change_exp_date':
                    document.getElementById("new_billing_profile_id").required = false;
                    document.getElementById("new_exp_date").required = true;
                    document.getElementById("new_operator_id").required = false;
                    document.getElementById("new_package_id").required = false;
                    document.getElementById("validity").required = false;
                    document.getElementById("new_zone_id").required = false;
                    document.getElementById("sms_body").required = false;
                    $("#select_package_option").hide();
                    $("#select_operator_option").hide();
                    $("#select_exp_date").show();
                    $("#select_billing_profile_option").hide();
                    $("#select_validity").hide();
                    $("#select_zone_option").hide();
                    $("#type_sms_body").hide();
                    $("#btn-submit").attr("disabled", false);
                    break;
                case 'edit_zone':
                    if ($("#new_billing_profile_id").length) {
                        document.getElementById("new_billing_profile_id").required = false;
                    }
                    if ($("#new_exp_date").length) {
                        document.getElementById("new_exp_date").required = false;
                    }
                    if ($("#new_operator_id").length) {
                        document.getElementById("new_operator_id").required = false;
                    }
                    if ($("#new_package_id").length) {
                        document.getElementById("new_package_id").required = false;
                    }
                    document.getElementById("validity").required = false;
                    document.getElementById("new_zone_id").required = true;
                    document.getElementById("sms_body").required = false;
                    if ($("#select_package_option").length) {
                        $("#select_package_option").hide();
                    }
                    if ($("#select_operator_option").length) {
                        $("#select_operator_option").hide();
                    }
                    if ($("#select_exp_date").length) {
                        $("#select_exp_date").hide();
                    }
                    if ($("#select_billing_profile_option").length) {
                        $("#select_billing_profile_option").hide();
                    }
                    $("#select_validity").hide();
                    $("#select_zone_option").show();
                    $("#type_sms_body").hide();
                    $("#btn-submit").attr("disabled", false);
                    break;
                case 'send_sms':
                    if ($("#new_billing_profile_id").length) {
                        document.getElementById("new_billing_profile_id").required = false;
                    }
                    if ($("#new_exp_date").length) {
                        document.getElementById("new_exp_date").required = false;
                    }
                    if ($("#new_operator_id").length) {
                        document.getElementById("new_operator_id").required = false;
                    }
                    if ($("#new_package_id").length) {
                        document.getElementById("new_package_id").required = false;
                    }
                    document.getElementById("validity").required = false;
                    document.getElementById("new_zone_id").required = false;
                    document.getElementById("sms_body").required = true;
                    if ($("#select_package_option").length) {
                        $("#select_package_option").hide();
                    }
                    if ($("#select_operator_option").length) {
                        $("#select_operator_option").hide();
                    }
                    if ($("#select_exp_date").length) {
                        $("#select_exp_date").hide();
                    }
                    if ($("#select_billing_profile_option").length) {
                        $("#select_billing_profile_option").hide();
                    }
                    $("#select_validity").hide();
                    $("#select_zone_option").hide();
                    $("#type_sms_body").show();
                    $("#btn-submit").attr("disabled", false);
                    break;
                case 'extend_package_validity':
                    if ($("#new_billing_profile_id").length) {
                        document.getElementById("new_billing_profile_id").required = false;
                    }
                    if ($("#new_exp_date").length) {
                        document.getElementById("new_exp_date").required = false;
                    }
                    if ($("#new_operator_id").length) {
                        document.getElementById("new_operator_id").required = false;
                    }
                    if ($("#new_package_id").length) {
                        document.getElementById("new_package_id").required = false;
                    }
                    document.getElementById("validity").required = true;
                    document.getElementById("new_zone_id").required = false;
                    document.getElementById("sms_body").required = false;
                    if ($("#select_package_option").length) {
                        $("#select_package_option").hide();
                    }
                    if ($("#select_operator_option").length) {
                        $("#select_operator_option").hide();
                    }
                    if ($("#select_exp_date").length) {
                        $("#select_exp_date").hide();
                    }
                    if ($("#select_billing_profile_option").length) {
                        $("#select_billing_profile_option").hide();
                    }
                    $("#select_zone_option").hide();
                    $("#type_sms_body").hide();
                    $("#select_validity").show();
                    $("#btn-submit").attr("disabled", false);
                    break;
                default:
                    if ($("#new_package_id").length) {
                        document.getElementById("new_package_id").required = false;
                    }
                    if ($("#new_operator_id").length) {
                        document.getElementById("new_operator_id").required = false;
                    }
                    if ($("#new_exp_date").length) {
                        document.getElementById("new_exp_date").required = false;
                    }
                    if ($("#new_billing_profile_id").length) {
                        document.getElementById("new_billing_profile_id").required = false;
                    }
                    if ($("#validity").length) {
                        document.getElementById("validity").required = false;
                    }
                    document.getElementById("new_zone_id").required = false;
                    document.getElementById("sms_body").required = false;
                    if ($("#select_package_option").length) {
                        $("#select_package_option").hide();
                    }
                    if ($("#select_operator_option").length) {
                        $("#select_operator_option").hide();
                    }
                    if ($("#select_exp_date").length) {
                        $("#select_exp_date").hide();
                    }
                    if ($("#select_billing_profile_option").length) {
                        $("#select_billing_profile_option").hide();
                    }
                    if ($("#select_validity").length) {
                        $("#select_validity").hide();
                    }
                    $("#select_zone_option").hide();
                    $("#type_sms_body").hide();
                    $("#btn-submit").attr("disabled", false);
            }
        }

        function showSpecialPrice(url) {
            $.get(url, function(data) {
                $("#modal-title").html("Special Price");
                $("#ModalBody").html(data);
                $('#modal-customer').modal('show');
            });
        }

        function showActivateOptions(url) {
            $.get(url, function(data) {
                $("#ActivateOptionsModelContent").html(data);
                $('#ActivateOptionsModalCenter').modal('show');
            });
        }
    </script>
@endsection
