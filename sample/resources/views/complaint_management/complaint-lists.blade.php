@section('content')

<div class="card">

    <!--modal -->
    <div class="modal fade" id="modal-customer">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="ModalBody">

                    <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>
                    <div class="text-bold pt-2">Loading...</div>
                    <div class="text-bold pt-2">Please Wait</div>

                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /modal -->

    <div class="card-body">

        <table id="phpPaging" class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Username</th>
                    <th scope="col">Department</th>
                    <th scope="col">Category</th>
                    <th scope="col">Status</th>
                    <th scope="col">Acknowledge By</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>

                @foreach ($complaints as $complaint )
                <tr>
                    <th scope="row">{{ $complaint->id }}</th>
                    <td>
                        <a href="#" onclick="showCustomerDetails('{{ $complaint->customer_id }}')">
                            {{ $complaint->mobile }}
                        </a>
                    </td>
                    <td>{{ $complaint->username }}</td>
                    <td>{{ $complaint->department->name }}</td>
                    <td>{{ $complaint->category->name }}</td>
                    <td class="{{ $complaint->status_color }}">{{ $complaint->status }}</td>
                    @if ($complaint->ack_status )
                    <td>{{ $complaint->ackBy->name }}</td>
                    @else
                    <td></td>
                    @endif
                    <td>

                        {{-- Details --}}
                        <a href="{{ route('customer_complains.show', ['customer_complain' => $complaint->id]) }}">
                            <i class="fas fa-exchange-alt"></i>
                            Details
                        </a>
                        {{-- Details --}}
                        {{-- Acknowledge --}}
                        @if ($complaint->ack_status == 0)
                        <a
                            href="{{ route('customer_complains.acknowledge.create', ['customer_complain' => $complaint->id]) }}">
                            <i class="fas fa-check"></i>
                            Acknowledge
                        </a>
                        @endif
                        {{-- Acknowledge --}}

                    </td>
                </tr>
                @endforeach

            </tbody>

        </table>

    </div>
    <!--/card body-->

    <div class="card-footer">
        <div class="row">
            <div class="col-sm-6">
                {{ $complaints->withQueryString()->links() }}
            </div>

        </div>
    </div>
    <!--/card-footer-->

</div>

@endsection

@section('pageJs')
<script>
    function showCustomerDetails(customer)
    {
        $("#modal-title").html("Customer Details");
        $("#ModalBody").html('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>');
        $("#ModalBody").append('<div class="text-bold pt-2">Loading...</div>');
        $("#ModalBody").append('<div class="text-bold pt-2">Please Wait</div>');
        $('#modal-customer').modal('show');
        $.get( "/admin/customer-details/" + customer, function( data ) {
            $("#ModalBody").html(data);
        });
    }
</script>
@endsection
