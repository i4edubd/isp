@section('contentTitle')
<h3> Assign Service </h3>
@endsection

@section('content')

<div class="card">

    <div class="card-header">
        <h3 class="card-title font-weight-bold">Reseller: {{ $operator->name }}</h3>
    </div>

    <form id="quickForm" method="POST"
        action="{{ route('operators.other_services.store', ['operator' => $operator->id]) }}">

        @csrf

        <div class="card-body">

            @foreach ($services as $service)
            <div class="form-check">
                <input name="service_id" class="form-check-input" type="radio" value="{{ $service->id }}"
                    id="{{ $service->id }}">
                <label class="form-check-label" for="{{ $service->id }}">
                    {{ $service->name }}
                </label>
            </div>
            @endforeach

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary mt-2">Next</button>
        </div>

    </form>

</div>

@endsection

@section('pageJs')
@endsection