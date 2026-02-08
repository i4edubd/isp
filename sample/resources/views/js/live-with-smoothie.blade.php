<script src="/jsPlugins/smoothie.js"></script>
<script>
    function showOff() {
        let interval_id = $('#show_id').val();
        if (interval_id.length) {
            clearInterval(interval_id);
            $('#show_id').val("");
        }
    }

    function monitorTraffic(show_url) {

        $('#modal-traffic').modal('show');

        var smoothie = new SmoothieChart({
            grid: {
                fillStyle: '#e8cece'
            },
            labels: {
                fillStyle: '#f40101',
                fontSize: 20,
                precision: 5
            }
        });

        smoothie.streamTo(document.getElementById("mikrotik-live-traffic"));

        var smoothie_download = new TimeSeries();

        var smoothie_upload = new TimeSeries();

        let interval_id = setInterval(function() {

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
                smoothie_upload.append(Date.now(), upload_bps);

                var readable_download = json_obj.readable_download;
                var download_bps = parseInt(json_obj.download);
                $('#live_download').html(readable_download);
                smoothie_download.append(Date.now(), download_bps);

                if (status != "Online") {
                    showOff();
                }

            }).fail(function() {
                showOff();
            });

        }, 3000);

        $('#show_id').val(interval_id);

        smoothie.addTimeSeries(smoothie_download, {
            lineWidth: 2,
            strokeStyle: '#007E33'
        });
        smoothie.addTimeSeries(smoothie_upload, {
            lineWidth: 2,
            strokeStyle: '#0d47a1'
        });

    }
</script>
