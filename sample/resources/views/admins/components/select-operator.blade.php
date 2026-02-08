<select class="form-control" name="operator_id" id="operator_id" required>
    <option value="">select operator...</option>
    @foreach ($operators as $operator)
    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
    @endforeach
</select>
