<div class="card-body">
    <div class="flex items-center gap-2">
        <h5 class="text-sm font-semibold inline-flex px-3 py-1 bg-rose-600 text-white rounded">LIVE</h5>
    </div>

    <ul class="mt-3 space-y-2 text-sm">
        <li class="flex justify-between">
            <span class="font-medium">Status:</span>
            <span class="text-emerald-600 font-medium">Online</span>
        </li>
        <li class="flex justify-between">
            <span class="font-medium">Download:</span>
            <span id="live_download" class="font-medium">{{ $live_data->get('readable_download') }}</span>
        </li>
        <li class="flex justify-between">
            <span class="font-medium">Upload:</span>
            <span id="live_upload" class="font-medium">{{ $live_data->get('readable_upload') }}</span>
        </li>
    </ul>
</div>
