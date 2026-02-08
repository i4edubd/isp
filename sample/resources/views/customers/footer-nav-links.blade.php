<div class="card-body">
    <a href="{{ route('customers.card-recharge.create') }}" class="btn btn-outline-info btn-sm mb-2">
        {{ getLocaleString($operator->id, 'Card Recharge') }}
    </a>
    <a href="{{ route('customers.packages') }}" class="btn btn-outline-info btn-sm mb-2">
        {{ getLocaleString($operator->id, 'Buy Package') }}
    </a>
    <a href="{{ route('customers.profile') }}" class="btn btn-outline-info btn-sm mb-2">
        {{ getLocaleString($operator->id, 'Profile') }}
    </a>
    <a href="{{ route('customers.edit-profile.create') }}" class="btn btn-outline-info btn-sm mb-2">
        {{ getLocaleString($operator->id, 'Edit Profile') }}
    </a>
</div>
