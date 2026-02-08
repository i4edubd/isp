@extends ('laraview.layouts.sideNavLayout')

@section('title')
    PPPoE Profiles
@endsection

@section('pageCss')
    <style>
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn {
            border-radius: 30px;
        }
        .table thead {
            background-color: #343a40;
            color: white;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table thead th {
            border: none;
        }
        .modal-header {
            background-color: #343a40;
            color: white;
        }
        .callout {
            border-left: 5px solid #343a40;
        }
    </style>
@endsection

@section('activeLink')
    @php
        $active_menu = '2';
        $active_link = '4';
    @endphp
@endsection

@section('sidebar')
    @include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
    <ul class="nav flex-column flex-sm-row">
        <!--PPPoE Profiles-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('pppoe_profiles.create') }}">
                <i class="fas fa-plus"></i>
                New PPP Profile
            </a>
        </li>
        <!--/PPPoE Profiles-->

        <!--upload pppoe profile-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('upload-ppp-profile') }}">
                <i class="fas fa-upload"></i>
                Upload PPP Profiles
            </a>
        </li>
        <!--/upload pppoe profile-->

        <li class="nav-item ml-4">
            <a class="btn btn-dark" href="{{ route('pppoe_profiles.index', ['unused' => 'yes']) }}" role="button">
                Unused Profiles
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div class="card">
        <!--modal -->
        <div class="modal fade" id="modal-default">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Packages</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="ModalBody">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /modal-content -->
            </div>
            <!-- /modal-dialog -->
        </div>
        <!-- /modal -->

        <div class="card-body">
            <table id="data_table" class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 2%">#</th>
                        <th scope="col">Profile Name</th>
                        <th scope="col">IPv4 Pool</th>
                        <th scope="col">IPv6 Pool</th>
                        <th scope="col">IPv4 Allocation</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($profiles as $profile)
                        <tr>
                            <th scope="row">{{ $profile->id }}</th>
                            <td>{{ $profile->name }}</td>
                            <td>{{ $profile->ipv4pool->name }} :: {{ long2ip($profile->ipv4pool->subnet) . '/' . $profile->ipv4pool->mask }}</td>
                            <td>{{ $profile->ipv6pool->name }} :: {{ $profile->ipv6pool->prefix }}</td>
                            <td>{{ $profile->ip_allocation_mode }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <a class="dropdown-item"
                                            href="{{ route('pppoe_profile_name.edit', ['pppoe_profile' => $profile->id]) }}">
                                            Change Name
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ route('pppoe_profile_ipv4pool.edit', ['pppoe_profile' => $profile->id]) }}">
                                            Change IPv4 Pool
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ route('pppoe_profile_ipv6pool.edit', ['pppoe_profile' => $profile->id]) }}">
                                            Change IPv6 Pool
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ route('pppoe_profile_ip_allocation_mode.edit', ['pppoe_profile' => $profile->id]) }}">
                                            Change IPv4 Allocation Mode
                                        </a>
                                        <a class="dropdown-item"
                                            href="{{ route('pppoe_profile_replace.edit', ['pppoe_profile' => $profile->id]) }}">
                                            Replace
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            onclick="showPackages('{{ route('pppoe_profiles.master_packages.index', ['pppoe_profile' => $profile->id]) }}')">
                                            Packages
                                        </a>
                                        @can('delete', $profile)
                                            <form method="post"
                                                action="{{ route('pppoe_profiles.destroy', ['pppoe_profile' => $profile->id]) }}">
                                                @csrf
                                                @method('delete')
                                                <button class="dropdown-item text-danger" type="submit">Delete</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            <p class="font-italic">Dynamic Vs. Static IPv4 Allocation: </p>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Dynamic IPv4 Allocation</th>
                        <th scope="col">Static IPv4 Allocation</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>The IP addresses of the users will be assigned by router depending on the ppp profile dynamically.</td>
                        <td>User IPs will be assigned by Radius Server based on the ppp profile statically.</td>
                    </tr>
                    <tr>
                        <td>The reseller may give the same ID to several customers or the customer may use the same ID on several devices.</td>
                        <td>The static IP address will prevent the reseller from giving the same ID to multiple clients or will prevent the client from using the same ID on multiple devices.</td>
                    </tr>
                    <tr>
                        <td>You need to create ppp profile both in router and software with the same name.</td>
                        <td>Do not need to create ppp profiles in the router.</td>
                    </tr>
                    <tr>
                        <td>ppp profiles cannot be synced with a router.</td>
                        <td>ppp profiles can be synced with a router.</td>
                    </tr>
                    <tr>
                        <td>Users IP could change with every session. So users cannot be traced with IP log.</td>
                        <td>Users will get the same IP address until package change.</td>
                    </tr>
                </tbody>
            </table>
            <div class="callout callout-warning">
                <p>Without Special requirement do not use Dynamic IPv4 Allocation mode</p>
                <p>For Dynamic IPv4 Allocation if you don't create ppp profile both in router and software with the same name, users will get 0.0.0.0 IP address and connection will be failed.</p>
                <p>Use either Dynamic or Static mode, mix mode is not recommended.</p>
            </div>
        </div>
    </div>
@endsection

@section('pageJs')
    <script>
        function showPackages(url) {
            $.get(url, function(data) {
                $("#ModalBody").html(data);
                $('#modal-default').modal('show');
            });
        }

        $(document).ready(function() {
            // Initialize Bootstrap dropdowns
            $('.dropdown-toggle').dropdown();
        });
    </script>
@endsection
