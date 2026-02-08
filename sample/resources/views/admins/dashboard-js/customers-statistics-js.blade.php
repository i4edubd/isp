<script>
	$(function() {

		let CustomerStatisticsChartUrl = "{{ route('customer_statistics_chart.index') }}";

		$.get(CustomerStatisticsChartUrl, function(data) {

			// Data
			let CustomerStatisticsChartData = jQuery.parseJSON(data);

			// labels
			let $labels = CustomerStatisticsChartData.labels;

			// total_paid_customers
			$({
				countNum: 0
			}).animate({
				countNum: CustomerStatisticsChartData.total_paid_customers_count
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_paid_customers').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_paid_customers').text(this.countNum);
				}
			});

			// total_billed_customers
			$({
				countNum: 0
			}).animate({
				countNum: CustomerStatisticsChartData.total_billed_customers_count
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_billed_customers').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_billed_customers').text(this.countNum);
				}
			});

			// total_suspended_customers
			$({
				countNum: 0
			}).animate({
				countNum: CustomerStatisticsChartData.total_suspended_customer_count
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_suspended_customers').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_suspended_customers').text(this.countNum);
				}
			});

			// total_active_customers
			$({
				countNum: 0
			}).animate({
				countNum: CustomerStatisticsChartData.total_active_customer_count
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_active_customers').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_active_customers').text(this.countNum);
				}
			});

			// total_disabled_customers
			$({
				countNum: 0
			}).animate({
				countNum: CustomerStatisticsChartData.total_disabled_customer_count
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_disabled_customers').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_disabled_customers').text(this.countNum);
				}
			});

			// total_online_customers
			$({
				countNum: 0
			}).animate({
				countNum: CustomerStatisticsChartData.total_online_customer_count
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_online_customers').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_online_customers').text(this.countNum);
				}
			});

			// paid-billed donut chart
			
			// paid-billed-bar-chart
			
			// online-offline donut chart
			var OnlineVsOfflineDonutChartCanvas = $('#OnlineVsOfflineDonutChart').get(0).getContext('2d');
			var donutData = {
				labels: [
					'Total Online',
					'Total Offline'
				],
				datasets: [{
					data: [CustomerStatisticsChartData.total_online_customer_count, CustomerStatisticsChartData.total_offline_customer_count],
					backgroundColor: ['rgba(40, 167, 69, 0.4)', 'rgba(0, 31, 63, 0.4)'],
					borderColor: ['rgba(40, 167, 69, 1)', 'rgba(0, 31, 63, 1)'],
				}]
			};

			var donutOptions = {
				maintainAspectRatio: false,
				responsive: true,
				plugins: {
					datalabels: {
						display: function(context) {
							if (context.dataset.data[context.dataIndex] < 10) {
								return 0;
							} else {
								return 'auto';
							}
						}
					}
				}
			};

			new Chart(OnlineVsOfflineDonutChartCanvas, {
				type: 'doughnut',
				plugins: [ChartDataLabels],
				data: donutData,
				options: donutOptions
			});

			// online-offline-bar-chart
			let online_data = CustomerStatisticsChartData.operators_online_customer_count;
			let offline_data = CustomerStatisticsChartData.operators_offline_customer_count;
			var $OnlineVsOfflineChart = $('#online-vs-offline-customers-chart');
			var OnlineVsOfflineChart = new Chart($OnlineVsOfflineChart, {
				type: 'bar',
				plugins: [ChartDataLabels],
				data: {
					labels: $labels,
					datasets: [{
							label: "Per Operator Online Customers",
							backgroundColor: 'rgba(40, 167, 69, 0.4)',
							borderColor: 'rgba(40, 167, 69, 1)',
							data: online_data,
							borderWidth: 1
						},
						{
							label: "Per Operator Offline Customers",
							backgroundColor: 'rgba(0, 31, 63, 0.4)',
							borderColor: 'rgba(0, 31, 63, 1)',
							data: offline_data,
							borderWidth: 1
						}
					]
				},
				options: {
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
					},
					scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true
						}
					}
				}
			});

			// customer-status-pie-chart
			var CustomerStatusPieChartCanvas = $('#CustomerStatus-pieChart').get(0).getContext('2d');
			var CustomerStatusPieData = {
				labels: [
					'Total active',
					'Total suspended',
					'Total disabled'
				],
				datasets: [{
					data: [CustomerStatisticsChartData.total_active_customer_count, CustomerStatisticsChartData.total_suspended_customer_count, CustomerStatisticsChartData.total_disabled_customer_count],
					backgroundColor: ['rgba(40, 167, 69, 0.4)', 'rgba(0, 31, 63, 0.4)', 'rgba(255, 0, 0, 0.4)'],
					borderColor: ['rgba(40, 167, 69, 1)', 'rgba(0, 31, 63, 1)', 'rgba(255, 0, 0, 1)']
				}]
			};

			var CustomerStatusPieOptions = {
				maintainAspectRatio: false,
				responsive: true,
				plugins: {
					datalabels: {
						display: function(context) {
							if (context.dataset.data[context.dataIndex] < 10) {
								return 0;
							} else {
								return 'auto';
							}
						}
					}
				}
			};

			new Chart(CustomerStatusPieChartCanvas, {
				type: 'pie',
				plugins: [ChartDataLabels],
				data: CustomerStatusPieData,
				options: CustomerStatusPieOptions
			});

			// total customer info
			$({
				countNum: 0
			}).animate({
				countNum: CustomerStatisticsChartData.total_customer_count
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_customer').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_customer').text(this.countNum);
				}
			});

			// customer-status-bar-chart
			let active_customer_data = CustomerStatisticsChartData.operators_active_customer_count;
			let suspended_customer_data = CustomerStatisticsChartData.operators_suspended_customer_count;
			let disabled_customer_data = CustomerStatisticsChartData.operators_disabled_customer_count;

			var $CustomerStatusChart = $('#CustomerStatus-stackedBarChart');

			var CustomerStatusChart = new Chart($CustomerStatusChart, {
				type: 'bar',
				plugins: [ChartDataLabels],
				data: {
					labels: $labels,
					datasets: [{
							label: 'Per Operator Active Customers',
							backgroundColor: 'rgba(40, 167, 69, 0.4)',
							borderColor: 'rgba(40, 167, 69, 1)',
							data: active_customer_data,
							borderWidth: 1
						},
						{
							label: 'Per Operator Suspended Customers',
							backgroundColor: 'rgba(0, 31, 63, 0.4)',
							borderColor: 'rgba(0, 31, 63, 1)',
							data: suspended_customer_data,
							borderWidth: 1
						},
						{
							label: 'Per Operator Disabled Customers',
							backgroundColor: 'rgba(255, 0, 0, 0.4)',
							borderColor: 'rgba(255, 0, 0, 1)',
							data: disabled_customer_data,
							borderWidth: 1
						}
					]
				},
				options: {
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
					},
					responsive: true,
					interaction: {
						intersect: false,
					},
					scales: {
						x: {
							stacked: true,
						},
						y: {
							stacked: true
						}
					}
				}
			});

			// per-day-new-registration-line-chart
			var $newRegistrationLineChart = $('#per-day-new-registration-lineChart');

			var newRegistrationLineChart = new Chart($newRegistrationLineChart, {
				type: 'line',
				plugins: [ChartDataLabels],
				data: {
					labels: CustomerStatisticsChartData.date_labels,
					datasets: [{
						label: 'Per Day Total New Registration',
						data: CustomerStatisticsChartData.daily_new_customers_count,
						fill: false,
						borderColor: 'rgb(75, 192, 192)',
						tension: 0.1
					}]
				},
				options: {
					plugins: {
						datalabels: {
							display: function(context) {
								if (context.dataset.data[context.dataIndex] < 10) {
									return 0;
								} else {
									return 'auto';
								}
							}
						}
					}
				}
			});

			// per-operator-new-registration-bar-chart
			var $perOperatorNewRegistration = $('#per-operator-new-registration-barChart');

			var perOperatorNewRegistration = new Chart($perOperatorNewRegistration, {
				type: 'bar',
				plugins: [ChartDataLabels],
				data: {
					labels: $labels,
					datasets: [{
						label: "Per Operator New Registration",
						backgroundColor: 'rgba(153, 102, 255, 0.4)',
						borderColor: 'rgba(153, 102, 255, 1)',
						data: CustomerStatisticsChartData.operators_monthly_new_customers_count,
						borderWidth: 1
					}]
				},
				options: {
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
					},
					scales: {
						x: {
							beginAtZero: true
						}
					}
				}
			});

		});
	});

</script>
