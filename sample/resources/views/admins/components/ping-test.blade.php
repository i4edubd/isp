@section('content')
    <div class="card">

        <form action="{{ route('ping-test.store') }}" method="POST" onsubmit="disableDuplicateSubmit()">

            <div class="card-body">
                @csrf
                <div class="form-group">
                    <label>IP Address:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-laptop"></i></span>
                        </div>
                        <input type="text" name="ip_address" class="form-control" data-inputmask="'alias': 'ip'"
                            data-mask required>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button id="submit-button" type="submit" class="btn btn-dark">Submit</button>
            </div>

        </form>

    </div>

    @if ($response)
        <div class="card card-outline card-dark">
            <div class="card-header">
                <h3 class="card-title font-italic font-weight-bold">Ping Test Result:</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach ($response as $item)
                        @if (strlen($item))
                            <li class="list-group-item">{{ $item }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

@endsection

@section('pageJs')
    <script>
        $(function() {
            $('[data-mask]').inputmask()
        })
    </script>
@endsection
