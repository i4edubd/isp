@section('content')

<div class="card">
    <h5 class="card-header">Generated Invoice/Bill</h5>

    <div class="card-body">
        @include('admins.components.runtime-invoice')
    </div>

    <div class="card-footer">
        <a class="btn btn-dark"
            href="{{ route('temp_customers.customers.create', ['temp_customer' => $temp_customer->id ]) }}"
            role="button">NEXT<i class="fas fa-arrow-right"></i>
        </a>
    </div>

</div>

@endsection

@section('pageJs')
@endsection