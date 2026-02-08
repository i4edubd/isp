@section('contentTitle')
<h3>Customer zones</h3>
@endsection

@section('content')

<div class="card">

    <ul class="nav flex-column flex-sm-row">

        <!--Add New Zone-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('customer_zones.create') }}">
                <i class="fas fa-plus"></i>
                Add New Zone
            </a>
        </li>
        <!--/Add New Zone-->

    </ul>

    <div class="card-body">


        <table id="data_table" class="table table-hover">

            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Zone Name</th>
                    <th scope="col"></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($customer_zones as $customer_zone)

                <tr>
                    <td scope="row">{{ $customer_zone->id }}</td>
                    <td>{{ $customer_zone->name }}</td>

                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item"
                                    href="{{ route('customer_zones.edit', ['customer_zone' => $customer_zone->id]) }}">
                                    Edit
                                </a>


                                <form method="post"
                                    action="{{ route('customer_zones.destroy', ['customer_zone' => $customer_zone->id ]) }}"
                                    onsubmit="return confirm('Are you sure to Delete')">
                                    @csrf
                                    @method('delete')
                                    <button class="dropdown-item" type="submit">Delete</button>
                                </form>


                            </div>

                        </div>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>
    <!--/card-body-->

</div>

@endsection

@section('pageJs')
@endsection
