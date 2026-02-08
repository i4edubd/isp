<form method="post" action="{{ route('authenticate-operator-instance.store') }}">
    @csrf

    <p class="alert alert-warning"> You will be logged out and will be logged in as {{ $operator->name }} </p>

    <input type="hidden" name="operator_id" value="{{ $operator->id }}">

    <button type="submit" class="btn btn-dark">Never Mind <i class="far fa-check-circle"></i></button>

</form>
