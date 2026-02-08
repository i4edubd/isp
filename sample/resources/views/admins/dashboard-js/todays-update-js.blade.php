<script>
    $(document).ready(function() {

		$.get("/widgets/will_be_suspended", function(data) {
			$({
				countNum: 0
			}).animate({
				countNum: data
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#will_be_suspended').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#will_be_suspended').text(this.countNum);
				}
			});
		});

		$.get("/widgets/amount_to_be_collected", function(data) {
			$({
				countNum: 0
			}).animate({
				countNum: data
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#amount_to_be_collected').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#amount_to_be_collected').text(this.countNum);
				}
			});
		});

		$.get("/widgets/collected_amount", function(data) {
			$({
				countNum: 0
			}).animate({
				countNum: data
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#collected_amount').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#collected_amount').text(this.countNum);
				}
			});
		});

        $.get("/widgets/today_sms_sent", function(data) {
			$({
				countNum: 0
			}).animate({
				countNum: data
			}, {
				duration: 300,
				easing: 'linear',
				step: function() {
					$('#today_sms_sent').text(Math.floor(this.countNum));
				},
				complete: function() {
					$('#today_sms_sent').text(this.countNum);
				}
			});
		});
	});

</script>
