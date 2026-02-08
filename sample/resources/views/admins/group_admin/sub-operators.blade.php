@extends ('laraview.layouts.sideNavLayout')

@section('title')
Operators
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '1';
$active_link = '2';
@endphp
@endsection

@section('sidebar')
@include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
<h3>Sub-Resellers</h3>
@endsection

@section('breadcrumb')
<ol class="breadcrumb text-danger float-sm-right">
    <li class="breadcrumb-item">Resellers</li>
    <li class="breadcrumb-item active">Sub-Resellers</li>
</ol>
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
                    <th scope="col">Reseller</th>
                    <th scope="col">Sub-Reseller</th>
                    <th scope="col">Total User</th>
                    <th scope="col">Account Type</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sub_operators->groupBy('gid') as $gid => $sub_resellers)
                @foreach ($sub_resellers as $sub_reseller)
                <tr>
                    <td scope="row">{{ $sub_reseller->id }}</td>
                    <td>{{ $sub_reseller->group_admin->name }}</td>
                    <td>{{ $sub_reseller->name }}</td>
                    <td>{{ $sub_reseller->customers()->count() }}</td>
                    <td>{{ $sub_reseller->account_type_alias }}</td>
                    <td>{{ $sub_reseller->status }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>

    </div>
    <!--/card body-->

</div>

@endsection

@section('pageJs')
@endsection