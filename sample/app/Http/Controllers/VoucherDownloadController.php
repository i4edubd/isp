<?php

namespace App\Http\Controllers;

use App\Models\customer_payment;
use App\Models\Freeradius\customer;
use App\Models\operator;
use App\Models\package;
use Mpdf\Mpdf;

class VoucherDownloadController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\customer_payment  $customer_payment
     * @return \Illuminate\Http\Response
     */
    public function create(customer_payment $customer_payment)
    {
        $this->authorize('view', [$customer_payment]);

        $operator = operator::find($customer_payment->operator_id);
        $customer = customer::find($customer_payment->customer_id);
        $package = package::find($customer_payment->package_id);

        #<<envelope
        if (strlen($operator->company_logo)) {
            $logo = "<img src=/storage/" . $operator->company_logo . ">";
        } else {
            $logo = "";
        }

        $operator_address = "<b>From, </b> <br>" . $operator->address;
        $customer_address = "<b>To, </b> <br>" . $customer->address . "<br> IP Address: " . $customer->login_ip;
        #envelope>>

        $invoice_table = view('admins.components.voucher-table', [
            'customer_payment' => $customer_payment,
            'package' => $package,
        ]);

        #total_x=200 and total_y=280

        $mpdf = new Mpdf();

        #<<envelope x=10-200 y=40-75
        if (strlen($logo)) {
            $mpdf->WriteFixedPosHTML($logo, 10, 40, 40, 35); //x= 10-50, y=40-75
            $mpdf->WriteFixedPosHTML($operator_address, 60, 40, 65, 35); //x= 60-125, y=40-75
        } else {
            $mpdf->WriteFixedPosHTML($operator_address, 10, 40, 80, 35); //x= 10-90, y=40-75
        }
        $mpdf->WriteFixedPosHTML($customer_address, 135, 40, 65, 35, 'auto'); //x = 135-200, y=40-75
        #envelope>>

        #<<table x = 10-200 , y= 80-180(height=100)
        $mpdf->WriteFixedPosHTML($invoice_table, 10, 80, 190, 100, 'auto');
        #table>>

        $mpdf->WriteFixedPosHTML("<p>This is computer generated invoice signature not required.</p>", 10, 190, 190, 10); //x= 10-200, y=190-200

        $mpdf->SetWatermarkText('Paid', 0.1);
        $mpdf->showWatermarkText = true;

        $mpdf->Output();
    }
}
