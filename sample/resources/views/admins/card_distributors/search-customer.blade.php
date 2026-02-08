@extends ('laraview.layouts.sideNavLayoutForCardDistributors')

@section('title')
    Search Customer
@endsection

@section('activeLink')
    @php
        $active_menu = '1';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.card_distributors.sidebar')
@endsection

@section('contentTitle')
@endsection

@section('content')
    <h2 class="text-center display-4">Search Customer</h2>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form action="{{ route('card.search-customer.store') }}" method="POST" onsubmit="return disableDuplicateSubmit()">
                @csrf
                <div class="input-group">
                    <input type="search" id="mobile_serach" name="mobile" class="form-control form-control-lg"
                        placeholder="Type Mobile Number" required>
                    <div class="input-group-append">
                        <button type="submit" id="submit-button" class="btn btn-lg btn-default">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('pageJs')
    <script>
        $(document).ready(function() {
            $.ajax({
                url: "/card/mobiles-list"
            }).done(function(result) {
                let mobiles = jQuery.parseJSON(result);
                $("#mobile_serach").autocomplete({
                    source: mobiles,
                    autoFocus: true,
                    select: function(event, ui) {
                        var value = ui.item.value;
                        $("#mobile_serach").val(value);
                        $("#mobile_serach").blur();
                    }
                });
            });
        });
    </script>
@endsection
