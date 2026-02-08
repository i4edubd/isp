<div class="card-body">

    <h5 class="text-danger border border-danger">LIVE</h5>

    <ul class="list-group list-group-flush">

        <li class="list-group-item">
            <span class="font-weight-bold">Status: </span> <span class="text-success"> Online </span>
        </li>
        <li class="list-group-item">
            <span class="font-weight-bold">Download : </span>
            <span id="live_download">{{ $live_data->get('readable_download') }} </span>
        </li>
        <li class="list-group-item">
            <span class="font-weight-bold">Upload : </span>
            <span id="live_upload">{{ $live_data->get('readable_upload') }}</span>
        </li>
    </ul>

</div>
