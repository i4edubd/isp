@section('contentTitle')
<h3>walled-garden configuration</h3>
@endsection

@section('content')

<div class="card">

    <div class="card-header font-italic">
        <div class="row">
            <div class="col-sm">
                <div class="callout callout-info">
                    <dl>
                        <dt>
                            You need walled-garden configuration if and only if
                        </dt>
                        <dd>
                            <ol>
                                <li>
                                    Hotspot service is running on the router.
                                </li>
                                <li>
                                    And you have online payment gateways.
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
                            Also Note That
                        </dt>
                        <dd>
                            <ol>
                                <li>
                                    We will use firewall layer7-protocol to match payment gateways.
                                </li>
                                <li>
                                    The L7 matcher is very resource-intensive.
                                </li>
                            </ol>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <form id="quickForm" method="POST" action="{{ route('routers.walled-garden.store', ['router' => $router]) }}">

        <div class="card-body">

            @csrf

            <!--router-->
            <div class="form-group">
                <label for="router">Router</label>
                <input type="text" class="form-control" id="router"
                    value="{{ $router->nasname }} :: {{ $router->location }}" disabled>
            </div>
            <!--/router-->

            <!--action-->
            <div class="form-group">
                <label for="action">Action</label>
                <select name="action" id="action" class="form-control select2" required>
                    <option value="updateorcreate">Update</option>
                    <option value="delete_layer7">Delete only layer7 Rules</option>
                    <option value="delete">Delete all walled-garden Rules</option>
                </select>
            </div>
            <!--/action-->

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>

    </form>

</div>

@endsection

@section('pageJs')
@endsection