<ul class="nav justify-content-end">
    <li class="nav-item mr-4 font-italic font-weight-bold">
        {{ getLocaleString($operator->id, 'Helpline') }} : {{ $operator->helpline ?? '' }}
    </li>
    <li class="nav-item">
        <form class="form-inline" method="POST" action="{{ route('customers.web.logout') }}">
            @csrf
            <button type="submit" class="btn btn-dark mb-2"><i class="fas fa-sign-out-alt"></i> Logout</button>
        </form>
    </li>
</ul>
