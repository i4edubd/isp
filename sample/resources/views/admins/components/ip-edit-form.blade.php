<form id="edit-ip-form" method="POST" action="{{ route('customers.edit-ip.store', ['customer' => $customer]) }}">

    <div class="card-body">

        @csrf

        <!--name-->
        <div class="form-group">
            <label for="name">Customer Name</label>
            <input type="text" class="form-control" id="name" value="{{ $customer->name }}" readonly>
        </div>
        <!--/name-->

        <!--package-->
        <div class="form-group">
            <label for="package">Package Name</label>
            <input type="text" class="form-control" id="package" value="{{ $package->name }}" readonly>
        </div>
        <!--/package-->

        <!--IP Pool-->
        <div class="form-group">
            <label for="ippool">IP Pool</label>
            <input type="text" class="form-control" id="ippool" value="{{ $ippool }}" readonly>
        </div>
        <!--/IP Pool-->

        {{-- IP Address --}}
        <div class="form-group">
            <label for="login_ip">IP Address</label>
            <input name="login_ip" type="text" class="form-control" id="login_ip" value="{{ $customer->login_ip }}"
                required>
        </div>
        {{-- IP Address --}}

    </div>
    <!--/card-body-->

    <div class="card-footer">
        <button type="submit" id="btn-submit" class="btn btn-dark">SUBMIT</button>
    </div>
    <!--/card-footer-->
</form>