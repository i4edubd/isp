<form class="" action="{{ route('disabled_filters.store') }}" method="POST">
    @csrf

    <input type="hidden" name="model" value="{{ $model }}">

    @foreach ($filters->get('enabled') as $key => $value)
    <div class="form-check">
        <input type="checkbox" name="{{ $key }}" id="{{ $key }}" checked data-bootstrap-switch>
        <label class="form-check-label" for="{{ $key }}">
            {{ $value }}
        </label>
    </div>
    @endforeach

    @foreach ($filters->get('disabled') as $key => $value)
    <div class="form-check">
        <input type="checkbox" name="{{ $key }}" id="{{ $key }}" data-bootstrap-switch>
        <label class="form-check-label" for="{{ $key }}">
            {{ $value }}
        </label>
    </div>
    @endforeach

    <div class="form-check mt-2">
        <button type="submit" class="btn btn-dark">
            SUBMIT
        </button>
    </div>

</form>

<script>
    $(function () {
        $("input[data-bootstrap-switch]").each(function(){
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })
    })
</script>