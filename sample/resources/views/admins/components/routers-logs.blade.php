@section('contentTitle')
<ul class="nav justify-content-start">
    <li class="nav-item">
        <h5 class="nav-link"> Router Logs: {{ $router->nasname }} :: {{ $router->location }} </h5>
    </li>
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
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item"><a href="{{ route('routers.index') }}"> Routers </a></li>
    <li class="breadcrumb-item active">Logs</li>
</ol>
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('routers.logs.index', ['router' => $router->id]) }}"
    method="get">

    {{-- topics --}}
    <div class="form-group col-md-2">
        <select name="topics" id="topics" class="form-control">
            <option value=''>topics...</option>
            @foreach ($topics as $topic )
            <option>{{ $topic }}</option>
            @endforeach
        </select>
    </div>
    {{--topics --}}

    {{-- message_like --}}
    <div class="form-group col-md-2">
        <input type="text" name="message_like" id="message_like" class="form-control" placeholder="Message LIKE ...">
    </div>
    {{-- message_like --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>
{{-- @endFilter --}}

<div class="card">

    <div class="card-body">

        <table id="phpPaging" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col" style="width: 2%">#</th>
                    <th scope="col">Topics</th>
                    <th scope="col">Message</th>
                    <th scope="col">Time</th>
                </tr>
            </thead>
            <tbody>

                @foreach ($logs as $log )
                <tr>
                    <th scope="row">{{ $log->get('.id') }}</th>
                    <td>{{ $log->get('topics') }}</td>
                    <td>{{ $log->get('message') }}</td>
                    <td>{{ $log->get('time') }}</td>
                </tr>
                @endforeach

            </tbody>
        </table>

    </div>

    <div class="card-footer">

        <div class="row">

            <div class="col-sm-2">
                Total Entries: {{ $logs->total() }}
            </div>

            <div class="col-sm-6">
                {{ $logs->links() }}
            </div>

        </div>

    </div>
    <!--/card-footer-->

</div>

@endsection

@section('pageJs')
@endsection