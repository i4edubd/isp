<div class="flex justify-end items-center gap-4 text-sm">
    <div class="italic font-medium text-slate-600">
        {{ getLocaleString($operator->id, 'Helpline') }} : {{ $operator->helpline ?? '' }}
    </div>
    <div>
        <form method="POST" action="{{ route('customers.web.logout') }}">
            @csrf
            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-slate-800 text-white rounded text-sm hover:bg-slate-700">
                <span class="svg-icon svg-icon-2 me-2"><!--logout svg--></span>
                Logout
            </button>
        </form>
    </div>
</div>
