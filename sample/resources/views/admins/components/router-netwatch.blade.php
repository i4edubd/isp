@section('contentTitle')
    <h3>Netwatch configuration</h3>
@endsection

@section('content')
    <div class="card">

        <div class="card-header font-italic">
            <div class="row">
                <div class="col-sm">
                    <div class="callout callout-info">
                        <dl>
                            <dt>
                                If a router fails to ping the radius server:
                            </dt>
                            <dd>
                                <ol>
                                    <li>
                                        All ppp secrets will be enabled from the router.
                                    </li>
                                    <li>
                                        So that the authentication service will not be affected.
                                    </li>
                                </ol>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="callout callout-info">
                        <dl>
                            <dt>
                                If the router can ping the radius server successfully:
                            </dt>
                            <dd>
                                <ol>
                                    <li>
                                        All ppp secrets will be disabled from the router.
                                    </li>
                                    <li>
                                        All users that were authenticated locally from the router will be disconnected.
                                    </li>
                                </ol>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <form id="quickForm" method="POST" action="{{ route('routers.netwatch.store', ['router' => $router]) }}">

            <div class="card-body">

                @csrf

                <!--router-->
                <div class="form-group">
                    <label for="router">Router</label>
                    <input type="text" class="form-control" id="router"
                        value="{{ $router->nasname }} :: {{ $router->location }}" disabled>
                </div>
                <!--/router-->

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-dark">Add Netwatch</button>
            </div>

        </form>

    </div>
@endsection

@section('pageJs')
@endsection
