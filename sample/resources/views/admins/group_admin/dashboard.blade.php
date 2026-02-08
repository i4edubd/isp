@extends ('laraview.layouts.sideNavLayout')

@section('title')
    Dashboard
@endsection

@section('pageCss')
@endsection

@section('activeLink')
    @php
        $active_menu = '0';
        $active_link = '0';
    @endphp
@endsection

@section('sidebar')
    @include('admins.group_admin.sidebar')
@endsection

@section('contentTitle')
    <h5>Dashboard</h5>
@endsection

@section('content')
    <!-- Modal -->
    <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalCenterTitle">Welcome!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p> <i class="fas fa-exclamation-circle"></i> The minimum configuration is incomplete.</p>
                    <p> <i class="far fa-play-circle"></i> Please Click on <span class="text-primary">Let's Start </span> button to start configuration.</p>
                    <a class="btn btn-primary" href="{{ route('configuration.next', ['operator' => Auth::user()->id]) }}" role="button">Let's Start <i class="far fa-play-circle"></i></a>
                    <button class="btn btn-secondary" id="remindMeLaterBtn">Remind Me Later</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /modal -->

    {{-- Today's update --}}
    <div class="card card-outline card-danger border-left border-danger">
        <div class="card-header">
            <h3 class="card-title font-italic"> Today's update ({{ date('l, d F Y') }})</h3>
        </div>
        <div class="card-body">
            {{-- First row --}}
            <div class="row">
            {{-- List Customers --}}
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box border border-info" onclick="window.open('{{ route('customers.index') }}', '_blank')">
            <span class="info-box-icon bg-info"><i class="fas fa-list"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">List Customers</span>
            </div>
        </div>
    </div>
                {{-- Operator List --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box border border-primary" onclick="window.open('{{ route('operators.index') }}', '_blank')">
                        <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Operator List</span>
                        </div>
                    </div>
                </div>
                {{-- Recharge Card --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box border border-success" onclick="window.open('{{ route('recharge_cards.index') }}', '_blank')">
                        <span class="info-box-icon bg-success"><i class="fas fa-credit-card"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Recharge Card</span>
                        </div>
                    </div>
                </div>
                {{-- Operators Income --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="info-box border border-warning" onclick="window.open('{{ route('operators_incomes.index') }}', '_blank')">
                        <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Operators Income</span>
                        </div>
                    </div>
                </div>
                {{-- Will be suspended --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-dark" onclick="window.location.href='{{ route('customers.index', ['will_be_suspended' => 1]) }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="will_be_suspended">0</h3>
                                <p>Upcoming Suspensions</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
                {{-- Amount to be collected --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-dark" onclick="window.location.href='{{ route('customer_bills.index', ['due_date' => date(config('app.date_format'))]) }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="amount_to_be_collected">0</h3>
                                <p>Amount to be collected</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
                {{-- Collected Amount --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-dark" onclick="window.location.href='{{ route('customer_payments.index', ['date' => date(config('app.date_format'))]) }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="collected_amount">0</h3>
                                <p>Collected Amount</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </div>
                </div>
                {{-- SMS Sent --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-dark" onclick="window.location.href='{{ route('sms_histories.index') }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="today_sms_sent">0</h3>
                                <p>SMS Sent</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sms"></i>
                        </div>
                    </div>
                </div>
            </div>
            {{-- First row --}}
        </div>
    </div>
    {{-- Today's update --}}

    {{-- widgets --}}
    <div class="card card-outline card-info border-left border-info">
        <div class="card-body">
            {{-- Second row --}}
            <div class="row">
                {{-- Total online Customers --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-primary" onclick="window.location.href='{{ route('online_customers.index') }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="total_online_customers">0</h3>
                                <p>Online Customers</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-globe"></i>
                        </div>
                    </div>
                </div>
                {{-- Total Suspended Customers --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-primary" onclick="window.location.href='{{ route('customers.index', ['status' => 'suspended']) }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="total_suspended_customers">0</h3>
                                <p>Suspended Customers</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-sort-down"></i>
                        </div>
                    </div>
                </div>
                {{-- Total active Customers --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-primary" onclick="window.location.href='{{ route('customers.index', ['status' => 'active']) }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="total_active_customers">0</h3>
                                <p>Active Customers</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="far fa-thumbs-up"></i>
                        </div>
                    </div>
                </div>
                {{-- Total disabled Customers --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-primary" onclick="window.location.href='{{ route('customers.index', ['status' => 'disabled']) }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="total_disabled_customers">0</h3>
                                <p>Disabled Customers</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="far fa-thumbs-down"></i>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Second row --}}
            {{-- Third row --}}
            <div class="row">
                {{-- Customer Complaints --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-navy" onclick="window.location.href='{{ route('customer_complains.index') }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="customer_complaints">0</h3>
                                <p>Customer Complaints</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                </div>
                {{-- Total Paid Customers --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-navy" onclick="window.location.href='{{ route('customers.index', ['payment_status' => 'paid']) }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="total_paid_customers">0</h3>
                                <p>Paid Customers</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
                {{-- Total Billed Customers --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-navy" onclick="window.location.href='{{ route('customers.index', ['payment_status' => 'billed']) }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="total_billed_customers">0</h3>
                                <p>Billed Customers</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </div>
                </div>
                {{-- SMS Balance --}}
                <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="info-box bg-navy" onclick="window.location.href='{{ route('advance_sms_payments.create') }}'">
                        <div class="info-box-content">
                            <div class="inner">
                                <h3 id="sms_balance">
                                    {{ Auth::user()->sms_balance }}
                                    {{ config('consumer.currency') }}
                                </h3>
                                <p>SMS Balance</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" style="width: 0%"></div>
                            </div>
                            <span class="progress-description">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </span>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Third row --}}
        </div>
    </div>
    {{-- widgets --}}

    <!-- /card -->

    {{-- Charts --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-center">Customer Growth</h3>
                </div>
                <div class="card-body">
                    <canvas id="areaChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-center">Monthly Registrations</h3>
                </div>
                <div class="card-body">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-center">Customer Status</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="stackedBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-center">Bills vs Paid</h3>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="billsVsPaidChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Charts --}}
@endsection

@section('pageJs')
<script src="/jsPlugins/chart.js-3.7.0/package/dist/chart.min.js"></script>
<script src="/jsPlugins/chartjs-plugin-datalabels/chartjs-plugin-datalabels.min.js"></script>

@include('admins.dashboard-js.customers-statistics-js')
@include('admins.dashboard-js.todays-update-js')

<script>
    $(function () {
        let CustomerStatisticsChartUrl = "{{ route('customer_statistics_chart.index') }}";
        let BillsVsPaymentsChartUrl = "{{ route('bills_vs_payments_chart.index') }}";

        // Fetch data asynchronously
        $.when(
            $.get(CustomerStatisticsChartUrl),
            $.get(BillsVsPaymentsChartUrl)
        ).done(function (customerData, billsData) {
            let CustomerStatisticsChartData = jQuery.parseJSON(customerData[0]);
            let BillsVsPaymentsChartData = jQuery.parseJSON(billsData[0]);
            let $labels = CustomerStatisticsChartData.labels;

            // Area Chart
            var $areaChartCanvas = $('#areaChart');
            if ($areaChartCanvas.length) {
                var areaChartData = {
                    labels: $labels,
                    datasets: [{
                        label: 'Active Customers',
                        data: CustomerStatisticsChartData.operators_active_customer_count,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        fill: true
                    }]
                };

                var areaChartOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                display: false // Hide x-axis labels
                            }
                        }
                    }
                };

                new Chart($areaChartCanvas.get(0).getContext('2d'), {
                    type: 'line',
                    data: areaChartData,
                    options: areaChartOptions
                });
            }

            // Polar Chart for Customer Status
            var $polarChartCanvas = $('#stackedBarChart');
            if ($polarChartCanvas.length) {
                var polarChartData = {
                    labels: $labels,
                    datasets: [{
                        label: 'Online Customers',
                        backgroundColor: '#4CAF50',
                        data: CustomerStatisticsChartData.operators_online_customer_count
                    },
                    {
                        label: 'Offline Customers',
                        backgroundColor: '#FFEB3B',
                        data: CustomerStatisticsChartData.operators_offline_customer_count
                    },
                    {
                        label: 'Suspended Customers',
                        backgroundColor: '#F44336',
                        data: CustomerStatisticsChartData.operators_suspended_customer_count
                    }]
                };

                var polarChartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                display: false // Hide x-axis labels
                            }
                        },
                        y: {
                            stacked: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            display: function(context) {
                                if (context.dataset.data[context.dataIndex] < 10) {
                                    return 0;
                                } else {
                                    return 'auto';
                                }
                            },
                            rotation: 300,
                            anchor: 'center'
                        }
                    }
                };

                new Chart($polarChartCanvas.get(0).getContext('2d'), {
                    type: 'bar',
                    data: polarChartData,
                    options: polarChartOptions
                });
            }

            // Line Chart
            var $lineChartCanvas = $('#lineChart');
            if ($lineChartCanvas.length) {
                var lineChartData = {
                    labels: CustomerStatisticsChartData.date_labels,
                    datasets: [{
                        label: 'New Registrations',
                        data: CustomerStatisticsChartData.daily_new_customers_count,
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        fill: true,
                        // Add operator names here
                        operatorNames: CustomerStatisticsChartData.operator_names
                    }]
                };

                var lineChartOptions = {
                    maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                display: false // Hide x-axis labels
                            }
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var operatorName = dataset.operatorNames[tooltipItem.index];
                                var value = dataset.data[tooltipItem.index];
                                return operatorName + ': ' + value;
                            }
                        }
                    }
                };

                new Chart($lineChartCanvas.get(0).getContext('2d'), {
                    type: 'line',
                    data: lineChartData,
                    options: lineChartOptions
                });
            }

            // Bills vs Paid Chart
            var billsVsPaidChartData = {
                labels: BillsVsPaymentsChartData.labels,
                datasets: [{
                    label: 'Payment Due',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    data: BillsVsPaymentsChartData.bill_data,
                    borderWidth: 1
                },
                {
                    label: 'Collected Payment',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    data: BillsVsPaymentsChartData.payment_data,
                    borderWidth: 1
                }]
            };

            var billsVsPaidChartCanvas = $('#billsVsPaidChart');
            if (billsVsPaidChartCanvas.length) {
                var billsVsPaidChartOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                display: false // Hide x-axis labels
                            }
                        },
                        y: {
                            stacked: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            display: function(context) {
                                if (context.dataset.data[context.dataIndex] < 10) {
                                    return 0;
                                } else {
                                    return 'auto';
                                }
                            },
                            rotation: 300,
                            anchor: 'center'
                        }
                    }
                };

                new Chart(billsVsPaidChartCanvas.get(0).getContext('2d'), {
                    type: 'bar',
                    data: billsVsPaidChartData,
                    options: billsVsPaidChartOptions
                });
            }
        });
    });

    $(document).ready(function () {
        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        let role = "{{ Auth::user()->role }}";
        let url = "{{ route('configuration.check', ['operator' => Auth::user()->id ]) }}";
        if (role == "group_admin" || role == 'operator') {
            if (!getCookie('skipModal')) {
                $.get(url, function (data) {
                    if (data == 1) {
                        $('#modal-default').modal({
                            backdrop: 'static',
                            show: true
                        });
                    }
                });
            }
        }

        $('#remindMeLaterBtn').click(function () {
            setCookie('skipModal', 'true', 5);
            $('#modal-default').modal('hide');
        });
    });

</script>

@endsection
