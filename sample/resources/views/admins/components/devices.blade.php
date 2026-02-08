@section('contentTitle')
<h3>Devices</h3>
@endsection

@section('content')

<div class="card">

    <ul class="nav flex-column flex-sm-row ml-2">

        <!--Add New Zone-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('devices.create') }}">
                <i class="fas fa-plus"></i>
                Add New Device
            </a>
        </li>
        <!--/Add New Zone-->

    </ul>

    <div class="card-body">

        <table id="data_table" class="table table-hover">

            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Location</th>
                    <th scope="col"></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($devices as $device)

                <tr>
                    <td scope="row">{{ $device->id }}</td>
                    <td>{{ $device->name }}</td>
                    <td>{{ $device->location }}</td>

                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item" href="{{ route('devices.edit', ['device' => $device->id]) }}">
                                    Edit
                                </a>


                                <form method="post" action="{{ route('devices.destroy', ['device' => $device->id]) }}"
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
