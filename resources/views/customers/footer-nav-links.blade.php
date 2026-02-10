<div class="flex flex-wrap gap-2">
    <a href="{{ route('customers.card-recharge.create') }}" class="btn btn-outline-info btn-sm px-3 py-1 text-sm bg-white border rounded">{{ getLocaleString($operator->id, 'Card Recharge') }}</a>
    <a href="{{ route('customers.packages') }}" class="btn btn-outline-info btn-sm px-3 py-1 text-sm bg-white border rounded">{{ getLocaleString($operator->id, 'Buy Package') }}</a>
    <a href="{{ route('customers.profile') }}" class="btn btn-outline-info btn-sm px-3 py-1 text-sm bg-white border rounded">{{ getLocaleString($operator->id, 'Profile') }}</a>
    <a href="{{ route('customers.edit-profile.create') }}" class="btn btn-outline-info btn-sm px-3 py-1 text-sm bg-white border rounded">{{ getLocaleString($operator->id, 'Edit Profile') }}</a>
</div>
