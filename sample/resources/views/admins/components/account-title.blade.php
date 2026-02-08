<dl class="row">
    <dt class="px-1">Account Provider: </dt>
    <dd> {{ $account->provider->company }} ({{ $account->provider->name }}) </dd>
</dl>
<dl class="row">
    <dt class="px-1"> Account Owner: </dt>
    <dd> {{ $account->owner->company }} ({{ $account->owner->name }}) </dd>
</dl>
<dl class="row">
    <dt class="px-1"> Account Balance: </dt>
    <dd> {{ $account->balance }} </dd>
</dl>