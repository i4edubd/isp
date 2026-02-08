<script>
    $(function() {

		let BillsVsPaymentsChartUrl = "{{ route('bills_vs_payments_chart.index') }}";

		$.get(BillsVsPaymentsChartUrl, function(data) {

			let BillsVsPaymentsChartData = jQuery.parseJSON(data);

			$({
				countNum: 0
			}).animate({
				countNum: BillsVsPaymentsChartData.total_due
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_due').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_due').text(this.countNum);
				}
			});

			$({
				countNum: 0
			}).animate({
				countNum: BillsVsPaymentsChartData.total_payment
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_payment').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_payment').text(this.countNum);
				}
			});

			$({
				countNum: 0
			}).animate({
				countNum: BillsVsPaymentsChartData.total_due_percentage
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_due_percentage').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_due_percentage').text(this.countNum);
				}
			});

			$({
				countNum: 0
			}).animate({
				countNum: BillsVsPaymentsChartData.total_payment_percentage
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#total_payment_percentage').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#total_payment_percentage').text(this.countNum);
				}
			});

			$('#total_payment_progress').css('width', BillsVsPaymentsChartData.total_payment_percentage + "%");
			$('#total_due_progress').css('width', BillsVsPaymentsChartData.total_due_percentage + "%");

			let $labels = BillsVsPaymentsChartData.labels;
			let bill_data = BillsVsPaymentsChartData.bill_data;
			let payment_data = BillsVsPaymentsChartData.payment_data;

			var $billsVsPaymentsChart = $('#bills-vs-payments')

			var billsVsPaymentsChart = new Chart($billsVsPaymentsChart, {
				type: 'bar',
				plugins: [ChartDataLabels],
				data: {
					labels: $labels,
					datasets: [{
							label: 'Payment Due',
							backgroundColor: 'rgba(255, 99, 132, 0.4)',
							borderColor: 'rgba(255,99,132,1)',
							data: bill_data,
							borderWidth: 1
						},
						{
							label: 'Collected Payment',
							backgroundColor: 'rgba(75, 192, 192, 0.4)',
							borderColor: 'rgba(75, 192, 192, 1)',
							data: payment_data,
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
					maintainAspectRatio: false,
					options: {
						scales: {
							y: {
								beginAtZero: true
							}
						}
					}
				}
			});
		});
	});

</script>
