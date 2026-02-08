<script src="/jsPlugins/chart.js-3.7.0/package/dist/chart.min.js"></script>
<script src="/jsPlugins/chartjs-plugin-streaming/luxon.min.js"></script>
<script src="/jsPlugins/chartjs-plugin-streaming/chartjs-adapter-luxon.js"></script>
<script src="/jsPlugins/chartjs-plugin-streaming/chartjs-plugin-streaming.min.js"></script>
<script>
    function showOff() {
        const chart = Chart.getChart("mikrotik-live-traffic");
        if (typeof chart != "undefined") {
            chart.destroy();
        }
        let interval_id = $('#show_id').val();
        if (interval_id.length) {
            clearInterval(interval_id);
            $('#show_id').val("");
        }
    }

    function monitorTraffic(show_url) {

        $('#modal-traffic').modal('show');

        const config = {
            type: 'line',
            data: {
                datasets: [{
                        label: 'Upload',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        borderColor: 'rgb(255, 99, 132)',
                        borderDash: [8, 4],
                        fill: true,
                        data: []
                    },
                    {
                        label: 'Download',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgb(54, 162, 235)',
                        cubicInterpolationMode: 'monotone',
                        fill: true,
                        data: []
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        type: 'realtime',
                        realtime: {
                            duration: 10000,
                            delay: 0,
                            frameRate: 30
                        }
                    }
                }
            }
        };

        const myChart = new Chart(
            document.getElementById('mikrotik-live-traffic'),
            config
        );

        var interval_id = setInterval(function() {

            $.ajax({
                url: show_url
            }).done(function(data) {

                var json_obj = jQuery.parseJSON(data);

                var name = json_obj.name;
                $('#live_name').html(name);

                var username = json_obj.username;
                $('#live_username').html(username);

                var package_name = json_obj.package_name;
                $('#live_package').html(package_name);

                var status = json_obj.status;
                $('#live_status').html(status);

                var readable_upload = json_obj.readable_upload;
                var upload_bps = parseInt(json_obj.upload);
                $('#live_upload').html(readable_upload);
                myChart.data.datasets[0].data.push({
                    x: Date.now(),
                    y: upload_bps
                });

                var readable_download = json_obj.readable_download;
                var download_bps = parseInt(json_obj.download);
                $('#live_download').html(readable_download);
                myChart.data.datasets[1].data.push({
                    x: Date.now(),
                    y: download_bps
                });

                myChart.update('quiet');

                if (status != "Online") {
                    showOff();
                }

            }).fail(function() {
                showOff();
            });
        }, 3000);

        $('#show_id').val(interval_id);
    }
</script>
