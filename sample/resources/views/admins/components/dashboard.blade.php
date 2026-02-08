@section('contentTitle')
<h3>Dashboard</h3>
@endsection

@section('content')

<!-- Modal -->
<div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle"
    aria-hidden="true">
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
                <p> <i class="far fa-play-circle"></i> Please Click on <span class="text-primary">Let's Start </span>
                    button to start configuration.</p>
                <a class="btn btn-primary" href="{{ route('configuration.next', ['operator' => Auth::user()->id]) }}"
                    role="button">Let's Start <i class="far fa-play-circle"></i></a>
            </div>
        </div>
    </div>
</div>
<!-- /modal -->

@can('viewWidgets')
@include('admins.components.dashboard-widgets')
@endcan

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-center">Customer Growth</h3>
            </div>
            <div class="card-body">
                <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title text-center">Monthly Registrations</h3>
            </div>
            <div class="card-body">
                <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="chart">
            <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
        </div>
    </div>
</div>

@endsection

@section('pageJs')

@can('viewWidgets')
@include('admins.components.dashboard-js')
@endcan

<script src="/themes/adminlte3x/plugins/chart.js/Chart.min.js"></script>
<script src="/jsPlugins/chart.js-3.7.0/package/dist/chart.min.js"></script>
<script src="/jsPlugins/chartjs-plugin-datalabels/chartjs-plugin-datalabels.min.js"></script>

@include('admins.dashboard-js.customers-statistics-js')
@include('admins.dashboard-js.todays-update-js')

<script>
    $(document).ready(function() {
        let ChartUrl = "{{ route('admin.dashboard.chart') }}";

        $.ajax({
            url: ChartUrl,
            method: 'GET',
            success: function(data) {
                let chartData;
                try {
                    chartData = jQuery.parseJSON(data);
                } catch (e) {
                    console.error('Error parsing JSON data:', e);
                    return;
                }

                // Ensure chartData has the expected structure
                if (!chartData || !chartData.in || !chartData.out) {
                    console.error('Invalid chart data structure:', chartData);
                    return;
                }

                // Provide default values for missing properties
                chartData.growth = chartData.growth || {};
                chartData.registrations = chartData.registrations || {};

                console.log('Chart data:', chartData); // Debugging step

                // Customer Growth Chart
                var areaChartCanvas = $('#areaChart');
                if (areaChartCanvas.length) {
                    var areaChartContext = areaChartCanvas.get(0).getContext('2d');
                    var areaChartData = {
                        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        datasets: [{
                            label: 'Customer Growth',
                            backgroundColor: 'rgba(60,141,188,0.9)',
                            borderColor: 'rgba(60,141,188,1)',
                            pointBackgroundColor: 'rgba(60,141,188,1)',
                            pointBorderColor: '#3b8bba',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: 'rgba(60,141,188,1)',
                            data: [
                                chartData.growth.January || 0,
                                chartData.growth.February || 0,
                                chartData.growth.March || 0,
                                chartData.growth.April || 0,
                                chartData.growth.May || 0,
                                chartData.growth.June || 0,
                                chartData.growth.July || 0,
                                chartData.growth.August || 0,
                                chartData.growth.September || 0,
                                chartData.growth.October || 0,
                                chartData.growth.November || 0,
                                chartData.growth.December || 0
                            ]
                        }]
                    };
                    var areaChartOptions = {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                ticks: {
                                    display: false // Hide x-axis labels
                                }
                            }
                        }
                    };

                    new Chart(areaChartContext, {
                        type: 'line',
                        data: areaChartData,
                        options: areaChartOptions
                    });
                } else {
                    console.error('Customer growth chart canvas not found'); // Debugging step
                }

                // Monthly Registrations Chart
                var lineChartCanvas = $('#lineChart');
                if (lineChartCanvas.length) {
                    var lineChartContext = lineChartCanvas.get(0).getContext('2d');
                    var lineChartData = {
                        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        datasets: [{
                            label: 'Monthly Registrations',
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1,
                            data: [
                                chartData.registrations.January || 0,
                                chartData.registrations.February || 0,
                                chartData.registrations.March || 0,
                                chartData.registrations.April || 0,
                                chartData.registrations.May || 0,
                                chartData.registrations.June || 0,
                                chartData.registrations.July || 0,
                                chartData.registrations.August || 0,
                                chartData.registrations.September || 0,
                                chartData.registrations.October || 0,
                                chartData.registrations.November || 0,
                                chartData.registrations.December || 0
                            ]
                        }]
                    };
                    var lineChartOptions = {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                ticks: {
                                    display: false // Hide x-axis labels
                                }
                            }
                        }
                    };

                    new Chart(lineChartContext, {
                        type: 'line',
                        data: lineChartData,
                        options: lineChartOptions
                    });
                } else {
                    console.error('Monthly registrations chart canvas not found'); // Debugging step
                }

                // Bar Chart
                var barChartCanvas = $('#barChart');
                if (barChartCanvas.length) {
                    var barChartContext = barChartCanvas.get(0).getContext('2d');
                    var barChartData = {
                        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        datasets: [{
                                label: 'Cash In',
                                backgroundColor: 'rgba(40,167,69,0.9)',
                                borderColor: 'rgba(40,167,69,1)',
                                pointBackgroundColor: 'rgba(40,167,69,1)',
                                pointBorderColor: '#28a745',
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: 'rgba(40,167,69,1)',
                                data: [
                                    chartData.in.January || 0,
                                    chartData.in.February || 0,
                                    chartData.in.March || 0,
                                    chartData.in.April || 0,
                                    chartData.in.May || 0,
                                    chartData.in.June || 0,
                                    chartData.in.July || 0,
                                    chartData.in.August || 0,
                                    chartData.in.September || 0,
                                    chartData.in.October || 0,
                                    chartData.in.November || 0,
                                    chartData.in.December || 0
                                ]
                            },
                            {
                                label: 'Cash Out',
                                backgroundColor: 'rgba(255, 0, 0, 0.9)',
                                borderColor: 'rgba(255, 0, 0, 1)',
                                pointBackgroundColor: 'rgba(255, 0, 0, 1)',
                                pointBorderColor: '#ff0000',
                                pointHoverBackgroundColor: '#fff',
                                pointHoverBorderColor: 'rgba(255, 0, 0, 1)',
                                data: [
                                    chartData.out.January || 0,
                                    chartData.out.February || 0,
                                    chartData.out.March || 0,
                                    chartData.out.April || 0,
                                    chartData.out.May || 0,
                                    chartData.out.June || 0,
                                    chartData.out.July || 0,
                                    chartData.out.August || 0,
                                    chartData.out.September || 0,
                                    chartData.out.October || 0,
                                    chartData.out.November || 0,
                                    chartData.out.December || 0
                                ]
                            }
                        ]
                    };
                    var barChartOptions = {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            x: {
                                ticks: {
                                    display: false // Hide x-axis labels
                                }
                            }
                        }
                    };

                    new Chart(barChartContext, {
                        type: 'bar',
                        data: barChartData,
                        options: barChartOptions
                    });
                } else {
                    console.error('Bar chart canvas not found'); // Debugging step
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed:', textStatus, errorThrown);
            }
        });

        let role = "{{ Auth::user()->role }}";
        let url = "{{ route('configuration.check', ['operator' => Auth::user()->id ]) }}";
        if(role == "group_admin" || role == 'operator'){
            $.get(url, function( data ) {
                if(data == 1){
                    $('#modal-default').modal({
                        backdrop: 'static',
                        show: true
                    });
                }
            });
        }
    });
</script>

@endsection
