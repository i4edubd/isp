@section('contentTitle')
@endsection

@section('content')

    {{-- @Filter --}}
    <form class="d-flex align-content-start flex-wrap" action="{{ route('online_customers.index') }}" method="get">

        
        {{-- status --}}
        <div class="form-group col-md-2">
            <select name="status" id="status" class="form-control">
                <option value=''>status...</option>
                <option value='active'>active</option>
                <option value='suspended'>suspended</option>
                <option value='disabled'>disabled</option>
            </select>
        </div>
        {{-- status --}}

        {{-- payment_status --}}
        <div class="form-group col-md-2">
            <select name="payment_status" id="payment_status" class="form-control">
                <option value=''>payment status...</option>
                <option value='billed'>billed</option>
                <option value='paid'>paid</option>
            </select>
        </div>
        {{-- payment_status --}}

                
        {{-- package_id --}}
        <div class="form-group col-md-2">
            <select name="package_id" id="package_id" class="form-control">
                <option value=''>package...</option>
                @foreach (Auth::user()->assigned_packages->sortBy('name') as $package)
                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                @endforeach
            </select>
        </div>
        {{-- package_id --}}

        {{-- sortby --}}
        <div class="form-group col-md-2">
            <select name="sortby" id="sortby" class="form-control">
                <option value=''>Sort By...</option>
                <option value="username">Username</option>
                <option value="acctoutputoctets">Bandwidth Usage</option>
                <option value="acctsessiontime">UP Time</option>
            </select>
        </div>
        {{-- sortby --}}

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

        
        @if (Auth::user()->role == 'group_admin')
            {{-- operator --}}
            <div class="form-group col-md-2">
                <select name="operator_id" id="operator_id" class="form-control">
                    <option value=''>operator...</option>
                    @foreach (Auth::user()->operators->where('role', '!=', 'manager') as $operator)
                        <option value="{{ $operator->id }}"> {{ $operator->name }} </option>
                    @endforeach
                </select>
            </div>
            {{-- operator --}}
        @endif

        <div class="form-group col-md-1">
            <button type="submit" class="btn btn-dark">FILTER</button>
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


    {{-- Refresh --}}
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
    </ul>
    {{-- Refresh --}}

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

        <!--traffic modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="modal-traffic">

            <div class="modal-dialog" role="document">

                <div class="modal-content">

                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-title">
                            <span class="text-danger border border-danger">
                                LIVE
                            </span>
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="showOff()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <input type="hidden" id="show_id" value="">

                    <div class="modal-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <span class="font-weight-bold">Name: </span>
                                <span id="live_name"> </span>
                            </li>
                            <li class="list-group-item">
                                <span class="font-weight-bold">Username: </span>
                                <span id="live_username"> </span>
                            </li>
                            <li class="list-group-item">
                                <span class="font-weight-bold">Package Name: </span>
                                <span id="live_package"> </span>
                            </li>
                            <li class="list-group-item">
                                <span class="font-weight-bold">Status: </span>
                                <span id="live_status"> </span>
                            </li>
                            <li class="list-group-item">
                                <span class="font-weight-bold text-success">Download: </span>
                                <span id="live_download"></span>
                            </li>
                            <li class="list-group-item">
                                <span class="font-weight-bold text-primary">Upload: </span>
                                <span id="live_upload"></span>
                            </li>
                        </ul>
                        <canvas id="mikrotik-live-traffic" width="470" height="200">
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal"
                            onclick="showOff()">Close</button>
                    </div>

                </div>

            </div>

        </div>
        <!-- /traffic modal -->


        <div class="card-body">

            {{-- Realtime Search --}}
            <nav class="navbar justify-content-end">
                <form class="form-inline">
                    {{-- mobile_serach --}}
                    <input class="form-control mr-sm-2" id="mobile_serach" type="search" placeholder="Search Mobile..">
                    {{-- mobile_serach --}}

                    {{-- username_serach --}}
                    <input class="form-control mr-sm-2" id="username_serach" type="search"
                        placeholder="Search username..">
                    {{-- username_serach --}}
                </form>
            </nav>
            {{-- Realtime Search --}}

            <div id='search_result'></div>

            <form action="{{ route('bulk-mac-bind.store') }}" method="POST">
                @csrf

                <table id="phpPaging" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center;">
                                <input id="selectAll" type="checkbox">
                            </th>
                            <th scope="col">Username</th>
                            <th scope="col">MAC Addresses <br>IP Address</th>
                            <th scope="col">Download</th>
                            <th scope="col">Upload</th>
                            <th scope="col">UP Time</th>
                            <th scope="col">Updated At</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($radaccts as $radacct)
                            <tr id="row-{{ $radacct->id }}">
                                <td style="text-align: center;">
                                    <input type="checkbox" name="radacct_ids[]" value="{{ $radacct->id }}">
                                </td>
                                <td>
                                    <a href="#" onclick="showCustomerDetails('{{ $radacct->customer->id }}')">
                                        {{ $radacct->username }}
                                    </a>
                                </td>
                                <td>{{ $radacct->callingstationid }} <br> {{ $radacct->framedipaddress }} </td>
                                <td>{{ $radacct->acctoutputoctets / 1000 / 1000 / 1000 }} GB</td>
                                <td>{{ $radacct->acctinputoctets / 1000 / 1000 / 1000 }} GB</td>
                                <td>{{ sToHms($radacct->acctsessiontime) }}</td>
                                <td>{{ $radacct->acctupdatetime }}</td>
                                <td class="d-inline-flex">
                                    {{-- Live Traffic --}}
                                    @if ($radacct->customer->connection_type == 'PPPoE')
                                        <a class="btn btn-outline-info btn-sm mb-2" href="{{ '#row-' . $radacct->id }}"
                                            onclick="monitorTraffic('{{ route('interface-traffic.show', ['radacct' => $radacct->id]) }}')">
                                            <i class="fas fa-chart-area"></i>
                                            Traffic
                                        </a>
                                    @endif
                                    {{-- Live Traffic --}}
                                    {{-- MAC Bind --}}
                                    @if ($radacct->customer->mac_bind == '0')
                                        <a class="btn btn-outline-info btn-sm mb-2" href="{{ '#row-' . $radacct->id }}"
                                            id="{{ 'online_customers_mac_bind_' . $radacct->id }}"
                                            onclick="callURL('{{ route('mac-bind-create', ['radacct' => $radacct]) }}', '{{ 'online_customers_mac_bind_' . $radacct->id }}')">
                                            <i class="fas fa-user-lock"></i> MAC Bind
                                        </a>
                                    @endif
                                    {{-- MAC Bind --}}
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
                            <option value="mac_bind">MAC Bind</option>
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
                    Total Entries: {{ $radaccts->total() }}
                </div>
                <div class="col-sm-6">
                    {{ $radaccts->withQueryString()->links() }}
                </div>
            </div>
        </div>
        <!--/card-footer-->

    </div>

@endsection

@section('pageJs')
    @include('js.live-with-chartjs-plugin')
    <script>
        $(document).ready(function() {

            $("#selectAll").click(function() {
                $("input[type=checkbox]").prop('checked', $(this).prop('checked'));
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
                        serachOnlineCustomer(value);
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
                        serachOnlineCustomerUsername(value);
                    }
                });
            });

        });

        function serachOnlineCustomer(online_customer) {
            $("#search_result").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            if (online_customer.length > 1) {
                $.ajax({
                    url: "/admin/online_customers/mobile?mobile=" + online_customer
                }).done(function(data) {
                    $("#search_result").html(data);
                });
            } else {
                $("#search_result").html("");
            }
        }

        function serachOnlineCustomerUsername(customer_username) {
            $("#search_result").html('<div class="overlay"><i class="fas fa-sync-alt fa-spin"></i></div>');
            if (customer_username.length > 1) {
                $.ajax({
                    url: "/admin/online_customers/username?username=" + customer_username
                }).done(function(data) {
                    $("#search_result").html(data);
                });
            } else {
                $("#search_result").html("");
            }
        }

        function showCustomerDetails(customer) {
            $("#modal-title").html("Customer Details");
            $("#ModalBody").html('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Loading...</div>');
            $("#ModalBody").append('<div class="text-bold pt-2">Please Wait</div>');
            $('#modal-customer').modal('show');
            $.get("/admin/customer-details/" + customer, function(data) {
                $("#ModalBody").html(data);
            });
        }
    </script>
@endsection
