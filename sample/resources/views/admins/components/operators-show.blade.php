@section('contentTitle')

<h3> Operator: </h3>

@endsection

@section('content')

<div class="card">

    <div class="card-body">

        <dl class="row">
            <dt class="col-sm-4">ID</dt>
            <dd class="col-sm-8">{{ $operator->id }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Name</dt>
            <dd class="col-sm-8">{{ $operator->name }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Mobile</dt>
            <dd class="col-sm-8">{{ $operator->mobile }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Email</dt>
            <dd class="col-sm-8">
                @if ($operator->email_verified_at)
                <i class="far fa-check-circle"></i>
                @else
                <i class="far fa-times-circle"></i>
                @endif
                {{ $operator->email }}
            </dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Company Name</dt>
            <dd class="col-sm-8">{{ $operator->company }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Status</dt>
            <dd class="col-sm-8">{{ $operator->status }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Registration Timestamp</dt>
            <dd class="col-sm-8">{{ $operator->created_at }}</dd>
        </dl>

        @if (Auth::user()->role == 'developer')

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Self Provisioning Request</dt>
            <dd class="col-sm-8">{{ $operator->sp_request }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Self Deletion Request</dt>
            <dd class="col-sm-8">{{ $operator->sd_request }}</dd>
        </dl>

        <hr>

        <dl class="row">
            <dt class="col-sm-4">Marketing Email Count</dt>
            <dd class="col-sm-8">{{ $operator->mrk_email_count }}</dd>
        </dl>

        @endif

    </div>

</div>

@endsection

@section('pageJs')
@endsection
