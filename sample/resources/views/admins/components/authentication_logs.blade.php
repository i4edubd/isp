@section('contentTitle')
<h3> Authentication Logs </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <table id="data_table" class="table table-bordered table-striped" style="width:100%;">
            <thead>
                <tr>
                    <th>IP Address</th>
                    <th>Login At</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($authentication_logs as $authentication_log)
                <tr>
                    <td>{{ $authentication_log->ip_address }}</td>
                    <td>{{ $authentication_log->login_at }}</td>
                    <td>{{ $authentication_log->user_agent }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@endsection

@section('pageJs')
@endsection