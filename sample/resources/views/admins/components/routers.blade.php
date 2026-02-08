@section('content')
    <div class="card shadow-sm">

        <!--modal -->
        <div class="modal fade" id="modal-default" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modalLabel">PPPoE Profiles</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="ModalBody">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- /modal -->

        <div class="card-body">

            <table id="data_table" class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" style="width: 2%">#</th>
                        <th scope="col">IP</th>
                        <th scope="col">Description</th>
                        <th scope="col">Status</th>
                        <th scope="col">Last Checked</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($routers as $router)
                        <tr>
                            <th scope="row">{{ $router->id }}</th>
                            <td>{{ $router->nasname }}</td>
                            <td>{{ $router->description }}</td>
                            <td>
                                API: <span class="{{ $router->api_status == 'OK' ? 'text-success' : 'text-danger' }}">
                                    {{ $router->api_status }}
                                </span>
                                <br>
                                System Identity: <span
                                    class="{{ $router->api_status == 'OK' ? 'text-success' : 'text-danger' }}">
                                    {{ $router->identity_status }}
                                </span>
                            </td>
                            <td>{{ $router->api_last_check }}</td>
                            <td>

                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        <li><a class="dropdown-item"
                                            href="{{ route('routers.configuration.create', ['router' => $router->id]) }}">
                                            Configure
                                        </a></li>
                                        <li><a class="dropdown-item"
                                            href="{{ route('routers.show', ['router' => $router->id]) }}">
                                            Check API
                                        </a></li>
                                        <li><a class="dropdown-item"
                                            href="{{ route('routers.logs.index', ['router' => $router->id]) }}">
                                            View Logs
                                        </a></li>
                                        <li><a class="dropdown-item" href="#"
                                            onclick="showPPPoEProfiles({{ $router->id }})">
                                            PPPoE Profiles
                                        </a></li>
                                        <li><a class="dropdown-item"
                                            href="{{ route('routers.edit', ['router' => $router->id]) }}">
                                            Edit
                                        </a></li>
                                        <li><a class="dropdown-item"
                                            href="{{ route('routers.walled-garden.create', ['router' => $router]) }}">
                                            Walled Garden
                                        </a></li>
                                        @if (Auth::user()->role == 'group_admin')
                                            <li><a class="dropdown-item"
                                                href="{{ route('routers.netwatch.create', ['router' => $router]) }}">
                                                Radius Monitoring
                                            </a></li>
                                        @endif
                                        <li>
                                            <form method="post"
                                                action="{{ route('routers.destroy', ['router' => $router->id]) }}"
                                                onsubmit="return confirm('Are you sure to Delete?')">
                                                @csrf
                                                @method('delete')
                                                <button class="dropdown-item text-danger" type="submit">Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

        </div>

    </div>

    {{-- Notes --}}
    <div class="card card-outline card-danger shadow-sm mt-4">
        <div class="card-header bg-danger text-white">
            Notes:
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                Please do not change the router's system identity, If the hotspot service is running on your router.
            </li>
            <li class="list-group-item">
                Please do not change the router's system identity, If you want to monitor ppp users live traffic.
            </li>
            <li class="list-group-item">
                Please do not change the router's system identity, To disconnect suspended customers from software.
            </li>
            <li class="list-group-item">
                Router's system identity must not contain the "=" character.
            </li>
            <li class="list-group-item">
                Router's system identity must not contain the " " (Whitespace character) character.
            </li>
            <li class="list-group-item">
                If API Connection failed, Hotspot customer's authentication will be failed.
            </li>
        </ul>
    </div>
    {{-- Notes --}}
@endsection

@section('pageJs')
    <script>
        function showPPPoEProfiles(router) {
            $.get("/admin/routers/" + router + "/pppoe_profiles", function(data) {
                $("#ModalBody").html(data);
                $('#modal-default').modal('show');
            });
        }

        $(document).ready(function() {
            // Initialize Bootstrap dropdown
            $('.dropdown-toggle').dropdown();
        });
    </script>
@endsection
