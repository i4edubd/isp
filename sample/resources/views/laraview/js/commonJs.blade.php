<script type="text/javascript">
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 6000
    });
</script>

@if (session('success'))
    <script type="text/javascript">
        Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}'
        })
    </script>
@endif

@if (session('error'))
    <script type="text/javascript">
        Toast.fire({
            icon: 'error',
            title: '{{ session('error') }}'
        })
    </script>
@endif

@if (session('info'))
    <script type="text/javascript">
        Toast.fire({
            icon: 'info',
            title: '{{ session('info') }}'
        })
    </script>
@endif

@if (session('warning'))
    <script type="text/javascript">
        Toast.fire({
            icon: 'warning',
            title: '{{ session('warning') }}'
        })
    </script>
@endif

@if (Auth::user())
    <script>
        function globalSerachCustomer(query) {
            if (query.length > 1) {
                $("#global-search-modal-title").html("Customer Details");
                $("#GlobalSearchModalBody").html(
                    '<div class="overlay-wrapper"><div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>'
                );
                $('#modal-global-search').modal('show');
                $.get("/admin/global-customer-search/" + query, function(data) {
                    $("#GlobalSearchModalBody").html(data);
                });
            }
        }

        $(document).ready(function() {
            if ($("#global-customer-search").length) {
                $.ajax({
                    url: "/admin/global-customer-search",
                    cache: true
                }).done(function(result) {
                    let collections = jQuery.parseJSON(result);
                    $("#global-customer-search").autocomplete({
                        source: collections,
                        minLength: 2,
                        autoFocus: true,
                        position: {
                            my: "center top",
                            at: "center bottom+20"
                        },
                        select: function(event, ui) {
                            var label = ui.item.label;
                            var value = ui.item.value;
                            globalSerachCustomer(value);
                        }
                    });
                });
            }
        });
    </script>
@endif

<script>
    $('#data_table').DataTable({
        responsive: {
            details: true
        },
        "searching": true,
        "pageLength": 50,
        "lengthChange": true,
        "ordering": false,
        "autoWidth": false
    });

    $("#phpPaging").DataTable({
        "autoWidth": false,
        "info": false,
        "pageLength": 50,
        "lengthChange": false,
        "ordering": false,
        "paging": false,
        "searching": false,
        responsive: {
            details: true
        }
    });

    function showWait() {
        $('#ModalShowWait').modal({
            backdrop: 'static',
            show: true
        });
        return true;
    }

    function modalDataTable() {
        $('#modal_table').DataTable({
            responsive: {
                details: true
            },
            "searching": false,
            "pageLength": 50,
            "lengthChange": false,
            "ordering": false,
            "autoWidth": false
        });
    }

    function callURL(url, id) {
        let selector = "#" + id;
        $(selector).html('<i class="fas fa-sync-alt fa-spin"></i>');
        $(selector).prop("onclick", null).off("click", selector);
        $.get(url, function(data) {
            $(selector).html('Done!');
            Toast.fire({
                icon: 'success',
                title: data
            });
        });
    }

    function callUsersActionURL(url, id, customer = 0) {
        let selector = "#" + id;
        $(selector).html('<i class="fas fa-sync-alt fa-spin"></i>');
        $(selector).prop("onclick", null).off("click", selector);
        $.get(url, function(data) {
            $(selector).html('Done!');
            Toast.fire({
                icon: 'success',
                title: data
            });
        });

        if (customer) {
            if ($("#row-" + customer).length) {
                setTimeout(
                    function() {
                        $.get("/ajax/all-customers-row/" + customer, function(data) {
                            $("#row-" + customer).html(data);
                        });
                    }, 3000);

            }
        }
    }

    function disableDuplicateSubmit() {
        let selector = "#submit-button";
        $(selector).prop('disabled', true);
        $(selector).html('<i class="fas fa-sync-alt fa-spin"></i>');
        return true;
    }
</script>
