@section('contentTitle')

<ul class="nav flex-column flex-sm-row ml-4">
    <!--New Manager-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('managers.create') }}">
            <i class="fas fa-plus"></i>
            New Manager
        </a>
    </li>
    <!--/New Manager-->
</ul>

@endsection

@section('content')

<div class="card">

    <!--modal -->
    <div class="modal" tabindex="-1" role="dialog" id="modal-default">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modal-title" class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body overflow-auto" id="ModalBody">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /modal-content -->
        </div>
        <!-- /modal-dialog -->
    </div>
    <!-- /modal -->

    <div class="card-body">

        <table id="data_table" class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Permissions</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($managers as $manager)
                <tr>
                    <td scope="row">{{ $manager->id }}</td>
                    <td>{{ $manager->name }}</td>
                    <td>{{ $manager->email }}</td>
                    <td>
                        @foreach ($manager->permissions as $permission)
                        <span class="border border-info">{{ $permission }} </span>
                        @endforeach
                    </td>

                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item"
                                    href="{{ route('managers.edit', ['manager' => $manager->id]) }}">
                                    Edit
                                </a>

                                <a class="dropdown-item" href="#" onclick="getPanelAccess({{ $manager->id }})">
                                    Get Panel Access
                                </a>

                                <form method="post"
                                    action="{{ route('managers.destroy', ['manager' => $manager->id]) }}"
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
    <!--/card body-->

</div>

@endsection

@section('pageJs')
<script>
    function getPanelAccess(operator)
    {
        $.get( "/admin/authenticate-operator-instance/" + operator, function( data ) {
            $("#modal-title").html("Get Panel Access");
            $("#ModalBody").html(data);
            $('#modal-default').modal('show');
        });
    }
</script>
@endsection
