<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;

class mpdfTestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        // A4 measures 210 × 297 millimeters or 8.27 × 11.69 inches.

        $path = public_path('storage/AcaAdmitCardTemplate.pdf');

        $mpdf = new Mpdf();

        $pagecount = $mpdf->SetSourceFile($path);
        $tplIdx = $mpdf->ImportPage($pagecount);
        $mpdf->UseTemplate($tplIdx);

        // traveling $x and $w
        $mpdf->WriteFixedPosHTML("1", 1, 1, 1, 1);
        $mpdf->WriteFixedPosHTML("3", 3, 1, 1, 1);
        $mpdf->WriteFixedPosHTML("5", 5, 1, 1, 1);
        $mpdf->WriteFixedPosHTML("7", 7, 1, 1, 1);
        $mpdf->WriteFixedPosHTML("9", 9, 1, 1, 1);
        $mpdf->WriteFixedPosHTML("11", 11, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("17", 17, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("23", 23, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("29", 29, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("35", 35, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("41", 41, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("47", 47, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("53", 53, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("59", 59, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("65", 65, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("71", 71, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("77", 77, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("83", 83, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("89", 89, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("95", 95, 1, 5, 1);
        $mpdf->WriteFixedPosHTML("101", 101, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("109", 109, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("117", 117, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("125", 125, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("133", 133, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("141", 141, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("149", 149, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("157", 157, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("165", 165, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("173", 173, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("181", 181, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("189", 189, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("197", 197, 1, 7, 1);
        $mpdf->WriteFixedPosHTML("L", 208, 1, 1, 1);
        /*
        total width found 210
        per character = 2
        for space = 1
        start of next character = $x + $w + 1
        */

        // Traveling $y and $h
        $mpdf->WriteFixedPosHTML("1", 1, 6, 1, 1);
        $mpdf->WriteFixedPosHTML("12", 1, 12, 5, 1);
        $mpdf->WriteFixedPosHTML("18", 1, 18, 5, 1);
        $mpdf->WriteFixedPosHTML("24", 1, 24, 5, 1);
        $mpdf->WriteFixedPosHTML("30", 1, 30, 5, 1);
        $mpdf->WriteFixedPosHTML("36", 1, 36, 5, 1);
        $mpdf->WriteFixedPosHTML("42", 1, 42, 5, 1);
        $mpdf->WriteFixedPosHTML("48", 1, 48, 5, 1);
        $mpdf->WriteFixedPosHTML("54", 1, 54, 5, 1);
        $mpdf->WriteFixedPosHTML("60", 1, 60, 5, 1);
        $mpdf->WriteFixedPosHTML("66", 1, 66, 5, 1);
        $mpdf->WriteFixedPosHTML("72", 1, 72, 5, 1);
        $mpdf->WriteFixedPosHTML("78", 1, 78, 5, 1);
        $mpdf->WriteFixedPosHTML("84", 1, 84, 5, 1);
        $mpdf->WriteFixedPosHTML("90", 1, 90, 5, 1);
        $mpdf->WriteFixedPosHTML("96", 1, 96, 5, 1);
        $mpdf->WriteFixedPosHTML("102", 1, 102, 7, 1);
        $mpdf->WriteFixedPosHTML("108", 1, 108, 7, 1);
        $mpdf->WriteFixedPosHTML("114", 1, 114, 7, 1);
        $mpdf->WriteFixedPosHTML("120", 1, 120, 7, 1);
        $mpdf->WriteFixedPosHTML("126", 1, 126, 7, 1);
        $mpdf->WriteFixedPosHTML("132", 1, 132, 7, 1);
        $mpdf->WriteFixedPosHTML("138", 1, 138, 7, 1);
        $mpdf->WriteFixedPosHTML("144", 1, 144, 7, 1);
        $mpdf->WriteFixedPosHTML("150", 1, 150, 7, 1);
        $mpdf->WriteFixedPosHTML("156", 1, 156, 7, 1);
        $mpdf->WriteFixedPosHTML("162", 1, 162, 7, 1);
        $mpdf->WriteFixedPosHTML("168", 1, 168, 7, 1);
        $mpdf->WriteFixedPosHTML("174", 1, 174, 7, 1);
        $mpdf->WriteFixedPosHTML("180", 1, 180, 7, 1);
        $mpdf->WriteFixedPosHTML("186", 1, 186, 7, 1);
        $mpdf->WriteFixedPosHTML("192", 1, 192, 7, 1);
        $mpdf->WriteFixedPosHTML("198", 1, 198, 7, 1);
        $mpdf->WriteFixedPosHTML("204", 1, 204, 7, 1);
        $mpdf->WriteFixedPosHTML("210", 1, 210, 7, 1);
        $mpdf->WriteFixedPosHTML("216", 1, 216, 7, 1);
        $mpdf->WriteFixedPosHTML("222", 1, 222, 7, 1);
        $mpdf->WriteFixedPosHTML("228", 1, 228, 7, 1);
        $mpdf->WriteFixedPosHTML("234", 1, 234, 7, 1);
        $mpdf->WriteFixedPosHTML("240", 1, 240, 7, 1);
        $mpdf->WriteFixedPosHTML("246", 1, 246, 7, 1);
        $mpdf->WriteFixedPosHTML("252", 1, 252, 7, 1);
        $mpdf->WriteFixedPosHTML("258", 1, 258, 7, 1);
        $mpdf->WriteFixedPosHTML("264", 1, 264, 7, 1);
        $mpdf->WriteFixedPosHTML("270", 1, 270, 7, 1);
        $mpdf->WriteFixedPosHTML("276", 1, 276, 7, 1);
        $mpdf->WriteFixedPosHTML("282", 1, 282, 7, 1);
        $mpdf->WriteFixedPosHTML("291", 1, 291, 7, 1);

        /*
        total hight found 297
        character height = 6
        start of next line = $y + 6
        */

        $mpdf->WriteFixedPosHTML("TEST this sidfasfl fsakdfasfdl ", 89, 24, 100, 1);

        $mpdf->Output('test.pdf', "I");
    }
}
