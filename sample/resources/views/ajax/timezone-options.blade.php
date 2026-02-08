@foreach ($timezones as $timezone)
<option value="{{ $timezone->name }}">{{ $timezone->name }}</option>
@endforeach