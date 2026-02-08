@section('contentTitle')
<h3>Custom Fields</h3>
@endsection

@section('content')

<div class="card">

    <ul class="nav flex-column flex-sm-row">

        <!--Add New Field-->
        <li class="nav-item">
            <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('custom_fields.create') }}">
                <i class="fas fa-plus"></i>
                Add New Field
            </a>
        </li>
        <!--/Add New Field-->

    </ul>

    <div class="card-body">

        <table id="data_table" class="table table-hover">

            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Field Name</th>
                    <th scope="col"></th>
                </tr>
            </thead>

            <tbody>

                @foreach ($custom_fields as $custom_field)

                <tr>
                    <td scope="row">{{ $custom_field->id }}</td>
                    <td>{{ $custom_field->name }}</td>

                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item"
                                    href="{{ route('custom_fields.edit', ['custom_field' => $custom_field->id]) }}">
                                    Edit
                                </a>


                                <form method="post"
                                    action="{{ route('custom_fields.destroy', ['custom_field' => $custom_field->id ]) }}"
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
