@extends ('laraview.layouts.sideNavLayout')

@section('title')
Operators
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '3';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection


@section('contentTitle')
@endsection

@section('content')

{{-- @Filter --}}
<form class="d-flex align-content-start flex-wrap" action="{{ route('operators-delete.index') }}" method="get">

    {{-- sp_request --}}
    <div class="form-group col-md-2">
        <select name="sp_request" id="sp_request" class="form-control">
            <option value=''>sp request...</option>
            <option value='0'>0</option>
            <option value='1'>1</option>
        </select>
    </div>
    {{--sp_request --}}

    {{-- sd_request --}}
    <div class="form-group col-md-2">
        <select name="sd_request" id="sd_request" class="form-control">
            <option value=''>sd request...</option>
            <option value='0'>0</option>
            <option value='1'>1</option>
        </select>
    </div>
    {{--status --}}

    {{-- mrk_email_count --}}
    <div class="form-group col-md-2">
        <input type="text" name="mrk_email_count" id="mrk_email_count" class="form-control"
            placeholder="Marketing Email Count">
    </div>
    {{-- mrk_email_count --}}

    <div class="form-group col-md-2">
        <button type="submit" class="btn btn-dark">FILTER</button>
    </div>

</form>

{{-- @endFilter --}}

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
                    <th scope="col">SPR</th>
                    <th scope="col">SDR</th>
                    <th scope="col">MEC</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Company</th>
                    <th scope="col">Role</th>
                    <th scope="col">Radius Server</th>
                    <th scope="col">Total User</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($operators as $operator)
                <tr class="{{ $operator->color }}">
                    <th scope="row">{{ $operator->id }}</th>
                    <th>{{ $operator->sp_request }}</th>
                    <th>{{ $operator->sd_request }}</th>
                    <th>{{ $operator->mrk_email_count }}</th>
                    <th>{{ $operator->name }}</th>
                    <th>
                        @if ($operator->email_verified_at)
                        <i class="far fa-check-circle"></i>
                        @else
                        <i class="far fa-times-circle"></i>
                        @endif
                        {{ $operator->email }}
                    </th>
                    <td>{{ $operator->company }}</td>
                    <td>{{ $operator->role }}</td>
                    <td>{{ config('database.connections.' . $operator->radius_db_connection . '.host') }}</td>
                    <td>{{ $operator->group_customers()->count() }}</td>
                    <td>{{ $operator->status }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item"
                                    href="{{ route('operators-delete.show', ['operator' => $operator->id ]) }}">
                                    Details
                                </a>

                                <a class="dropdown-item"
                                    href="{{ route('operators.sales_comments.create', ['operator' => $operator->id ]) }}">
                                    Comments
                                </a>

                                <a class="dropdown-item" href="#" onclick="getPanelAccess({{ $operator->id }})">
                                    Get Panel Access
                                </a>

                            </div>

                        </div>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

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
