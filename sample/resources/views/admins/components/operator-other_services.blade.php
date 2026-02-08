@section('content')

<div class="card card-outline card-danger">

    <div class="card-header">
        <h3 class="card-title font-weight-bold">Operator/Reseller: {{ $operator->name }}</h3>
    </div>

</div>

{{-- Services --}}
<div class="card card-outline card-primary">

    <div class="card-header">
        <h3 class="card-title">Services</h3>
    </div>

    <div class="card-body">

        <div class="table-responsive-sm">

            <table class="table table-bordered">

                <thead>

                    <tr>
                        <th scope="col" style="width: 2%">#</th>
                        <th scope="col">Service Name</th>
                        <th scope="col">Customer Price</th>
                        <th scope="col">Operator Price</th>
                        <th scope="col">validity</th>
                        <th scope="col"></th>
                    </tr>

                </thead>

                <tbody>

                    @foreach ($other_services as $other_service)

                    <tr>
                        <td>{{ $other_service->id }}</td>
                        <td>{{ $other_service->name }}</td>
                        <td>{{ $other_service->price }}</td>
                        <td>{{ $other_service->operator_price }}</td>
                        <td>{{ $other_service->validity }} Days</td>
                        <td>

                            <div class="btn-group dropleft" role="group">

                                <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>

                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                    {{-- update --}}
                                    @can('operatorUpdate', $other_service)
                                    <a class="dropdown-item"
                                        href="{{ route('operators.other_services.edit', ['operator' => $operator->id, 'other_service' => $other_service->id]) }}">
                                        Edit
                                    </a>
                                    @endcan
                                    {{-- update --}}

                                    {{-- delete --}}
                                    @can('operatorDelete', $other_service)
                                    <form method="post"
                                        action="{{ route('operators.other_services.destroy', ['operator' => $operator->id, 'other_service' => $other_service->id]) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="dropdown-item text-danger" type="submit">Delete</button>
                                    </form>
                                    @endcan
                                    {{-- delete --}}

                                </div>

                            </div>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>
{{-- Services --}}

@endsection

@section('pageJs')
@endsection