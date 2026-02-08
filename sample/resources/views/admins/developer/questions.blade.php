@extends ('laraview.layouts.sideNavLayout')

@section('title')
Questions & Answers
@endsection

@section('pageCss')
@endsection

@section('activeLink')
@php
$active_menu = '11';
$active_link = '0';
@endphp
@endsection

@section('sidebar')
@include('admins.developer.sidebar')
@endsection


@section('contentTitle')

<ul class="nav flex-column flex-sm-row ml-4">
    <!--New Question-->
    <li class="nav-item">
        <a class="btn btn-outline-success my-2 my-sm-0" href="{{ route('questions.create') }}">
            <i class="fas fa-plus"></i>
            New Question
        </a>
    </li>
    <!--/New Question-->
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
                    <th scope="col">Question</th>
                    <th scope="col">Type</th>
                    <th scope="col">Mark</th>
                    <th scope="col">Lang</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questions as $question)
                <tr>
                    <td scope="row">{{ $question->id }}</td>
                    <td>
                        <a href="#"
                            onclick="showDetails('{{ route('questions.show', ['question' => $question->id]) }}')">
                            {{ $question->question }}
                        </a>
                    </td>
                    <td>{{ $question->type }}</td>
                    <td>{{ $question->mark }}</td>
                    <td>{{ $question->lang }}</td>
                    <td>

                        <div class="btn-group" role="group">

                            <button id="btnGroupDrop1" type="button" class="btn btn-secondary dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                <a class="dropdown-item"
                                    href="{{ route('questions.edit', ['question' => $question->id]) }}">
                                    Edit
                                </a>

                                <form method="post"
                                    action="{{ route('questions.destroy', ['question' => $question->id]) }}"
                                    onsubmit="return confirm('Are you sure to delete?')">
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

</div>

@endsection

@section('pageJs')
<script>
    function showDetails(url)
    {
        $.get( url, function( data ) {
            $("#modal-title").html("Details");
            $("#ModalBody").html(data);
            $('#modal-default').modal('show');
        });
    }
</script>
@endsection
