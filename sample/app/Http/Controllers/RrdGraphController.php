<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RrdGraphController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);

        $user_id =  encryptOrDecrypt('decrypt', $request->user_id);

        if (!$user_id) {
            return 0;
        }

        self::createHourlyGraph($user_id);
        self::createDailyGraph($user_id);
        self::createWeeklyGraph($user_id);
        self::createMonthlyGraph($user_id);

        return 1;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function create(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);

        $user_id =  encryptOrDecrypt('decrypt', $request->user_id);

        if (!$user_id) {
            return 0;
        }

        return self::store($user_id);
    }

    /**
     * Store a newly created resource in storage.
     * rrd-db directory should exists
     * rrd-db directory should owned by www-data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store(int $user_id)
    {
        //RRD DB
        $rrd_db = self::getRrdDb($user_id);

        if (file_exists($rrd_db)) {
            return 1;
        }

        // Data Source
        $ds_upload = 'upload' . $user_id;
        $ds_download = 'download' . $user_id;

        // options
        $opts = [
            "--step", "300",
            "DS:$ds_download:COUNTER:600:U:U",
            "DS:$ds_upload:COUNTER:600:U:U",
            "RRA:AVERAGE:0.5:1:1000",
            "RRA:AVERAGE:0.5:2:1500",
            "RRA:AVERAGE:0.5:3:3000",
            "RRA:AVERAGE:0.5:5:2000"
        ];

        return rrd_create($rrd_db, $opts);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public static function createHourlyGraph(int $user_id)
    {
        //RRD DB
        $rrd_db = self::getRrdDb($user_id);

        if (file_exists($rrd_db) == false) {
            self::store($user_id);
        }

        // Data Source
        $ds_upload = 'upload' . $user_id;
        $ds_download = 'download' . $user_id;

        //RRD Image
        $rrd_img_path = storage_path('app/public/rrd-img/');
        if (!is_dir($rrd_img_path)) {
            mkdir($rrd_img_path, 0755, true);
        }
        $rrd_img_name = 'h-graph' . $user_id . '.png';
        $rrd_img = $rrd_img_path . $rrd_img_name;

        //options
        $opts = array(
            "--start",
            "-1h",
            "--title",
            "User ID: $user_id",
            "--vertical-label",
            "bits per second",
            "DEF:dowload_avg=$rrd_db:$ds_download:AVERAGE",
            "DEF:upload_avg=$rrd_db:$ds_upload:AVERAGE",
            "CDEF:c_download_avg=dowload_avg,8,*",
            "CDEF:c_upload_avg=upload_avg,8,*",
            "AREA:c_download_avg#00FF00:Rx traffic",
            "LINE1:c_upload_avg#0000FF:Tx traffic\\r",
            "COMMENT:\\n",
            "GPRINT:c_upload_avg:AVERAGE:Avg Tx traffic\: %6.2lf %Sbps",
            "COMMENT:  ",
            "GPRINT:c_upload_avg:MAX:Max Tx traffic\: %6.2lf %Sbps\\r",
            "GPRINT:c_download_avg:AVERAGE:Avg Rx traffic\: %6.2lf %Sbps",
            "COMMENT: ",
            "GPRINT:c_download_avg:MAX:Max Rx traffic\: %6.2lf %Sbps\\r"
        );

        return rrd_graph($rrd_img, $opts);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public static function createDailyGraph(int $user_id)
    {
        //RRD DB
        $rrd_db = self::getRrdDb($user_id);

        // Data Source
        $ds_upload = 'upload' . $user_id;
        $ds_download = 'download' . $user_id;

        //RRD Image
        $rrd_img_path = storage_path('app/public/rrd-img/');
        if (!is_dir($rrd_img_path)) {
            mkdir($rrd_img_path, 0755, true);
        }
        $rrd_img_name = 'd-graph' . $user_id . '.png';
        $rrd_img = $rrd_img_path . $rrd_img_name;

        //options
        $opts = array(
            "--start",
            "-1d",
            "--title",
            "User ID: $user_id",
            "--vertical-label",
            "bits per second",
            "DEF:dowload_avg=$rrd_db:$ds_download:AVERAGE",
            "DEF:upload_avg=$rrd_db:$ds_upload:AVERAGE",
            "CDEF:c_download_avg=dowload_avg,8,*",
            "CDEF:c_upload_avg=upload_avg,8,*",
            "AREA:c_download_avg#00FF00:Rx traffic",
            "LINE1:c_upload_avg#0000FF:Tx traffic\\r",
            "COMMENT:\\n",
            "GPRINT:c_upload_avg:AVERAGE:Avg Tx traffic\: %6.2lf %Sbps",
            "COMMENT:  ",
            "GPRINT:c_upload_avg:MAX:Max Tx traffic\: %6.2lf %Sbps\\r",
            "GPRINT:c_download_avg:AVERAGE:Avg Rx traffic\: %6.2lf %Sbps",
            "COMMENT: ",
            "GPRINT:c_download_avg:MAX:Max Rx traffic\: %6.2lf %Sbps\\r"
        );
        return rrd_graph($rrd_img, $opts);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public static function createWeeklyGraph(int $user_id)
    {
        //RRD DB
        $rrd_db = self::getRrdDb($user_id);

        // Data Source
        $ds_upload = 'upload' . $user_id;
        $ds_download = 'download' . $user_id;

        //RRD Image
        $rrd_img_path = storage_path('app/public/rrd-img/');
        if (!is_dir($rrd_img_path)) {
            mkdir($rrd_img_path, 0755, true);
        }
        $rrd_img_name = 'w-graph' . $user_id . '.png';
        $rrd_img = $rrd_img_path . $rrd_img_name;

        //options
        $opts = array(
            "--start",
            "-1w",
            "--title",
            "User ID: $user_id",
            "--vertical-label",
            "bits per second",
            "DEF:dowload_avg=$rrd_db:$ds_download:AVERAGE",
            "DEF:upload_avg=$rrd_db:$ds_upload:AVERAGE",
            "CDEF:c_download_avg=dowload_avg,8,*",
            "CDEF:c_upload_avg=upload_avg,8,*",
            "AREA:c_download_avg#00FF00:Rx traffic",
            "LINE1:c_upload_avg#0000FF:Tx traffic\\r",
            "COMMENT:\\n",
            "GPRINT:c_upload_avg:AVERAGE:Avg Tx traffic\: %6.2lf %Sbps",
            "COMMENT:  ",
            "GPRINT:c_upload_avg:MAX:Max Tx traffic\: %6.2lf %Sbps\\r",
            "GPRINT:c_download_avg:AVERAGE:Avg Rx traffic\: %6.2lf %Sbps",
            "COMMENT: ",
            "GPRINT:c_download_avg:MAX:Max Rx traffic\: %6.2lf %Sbps\\r"
        );

        return rrd_graph($rrd_img, $opts);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public static function createMonthlyGraph(int $user_id)
    {
        //RRD DB
        $rrd_db = self::getRrdDb($user_id);

        // Data Source
        $ds_upload = 'upload' . $user_id;
        $ds_download = 'download' . $user_id;

        //RRD Image
        $rrd_img_path = storage_path('app/public/rrd-img/');
        if (!is_dir($rrd_img_path)) {
            mkdir($rrd_img_path, 0755, true);
        }
        $rrd_img_name = 'm-graph' . $user_id . '.png';
        $rrd_img = $rrd_img_path . $rrd_img_name;

        //options
        $opts = array(
            "--start",
            "-1m",
            "--title",
            "User ID: $user_id",
            "--vertical-label",
            "bits per second",
            "DEF:dowload_avg=$rrd_db:$ds_download:AVERAGE",
            "DEF:upload_avg=$rrd_db:$ds_upload:AVERAGE",
            "CDEF:c_download_avg=dowload_avg,8,*",
            "CDEF:c_upload_avg=upload_avg,8,*",
            "AREA:c_download_avg#00FF00:Rx traffic",
            "LINE1:c_upload_avg#0000FF:Tx traffic\\r",
            "COMMENT:\\n",
            "GPRINT:c_upload_avg:AVERAGE:Avg Tx traffic\: %6.2lf %Sbps",
            "COMMENT:  ",
            "GPRINT:c_upload_avg:MAX:Max Tx traffic\: %6.2lf %Sbps\\r",
            "GPRINT:c_download_avg:AVERAGE:Avg Rx traffic\: %6.2lf %Sbps",
            "COMMENT: ",
            "GPRINT:c_download_avg:MAX:Max Rx traffic\: %6.2lf %Sbps\\r"
        );

        return rrd_graph($rrd_img, $opts);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
        ]);

        $user_id =  encryptOrDecrypt('decrypt', $request->user_id);

        if (!$user_id) {
            return 0;
        }

        $rrd_db = self::getRrdDb($user_id);

        if (file_exists($rrd_db)) {
            unlink($rrd_db);
        }

        $rrd_img_path = storage_path('app/public/rrd-img/');

        // hourly image
        $rrd_img_name = 'h-graph' . $user_id . '.png';
        $rrd_img = $rrd_img_path . $rrd_img_name;
        if (file_exists($rrd_img)) {
            unlink($rrd_img);
        }

        // daily image
        $rrd_img_name = 'd-graph' . $user_id . '.png';
        $rrd_img = $rrd_img_path . $rrd_img_name;
        if (file_exists($rrd_img)) {
            unlink($rrd_img);
        }

        // weekly image
        $rrd_img_name = 'w-graph' . $user_id . '.png';
        $rrd_img = $rrd_img_path . $rrd_img_name;
        if (file_exists($rrd_img)) {
            unlink($rrd_img);
        }

        // monthly image
        $rrd_img_name = 'm-graph' . $user_id . '.png';
        $rrd_img = $rrd_img_path . $rrd_img_name;
        if (file_exists($rrd_img)) {
            unlink($rrd_img);
        }
    }


    /**
     * Get RRD Name
     *
     * @param  int  $user_id
     * @return string
     */
    public static function getRrdDb(int $user_id)
    {
        $rrd_db_path = storage_path('app/rrd-db/');

        if (!is_dir($rrd_db_path)) {
            mkdir($rrd_db_path, 0755, true);
        }

        $rrd_db_name = 'user' . $user_id . '.rrd';

        $rrd_db = $rrd_db_path . $rrd_db_name;

        return $rrd_db;
    }
}
