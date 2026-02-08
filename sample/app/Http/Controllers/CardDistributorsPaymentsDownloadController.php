<?php

namespace App\Http\Controllers;

use App\Models\card_distributor;
use App\Models\card_distributor_payments;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class CardDistributorsPaymentsDownloadController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $distributors = card_distributor::where('operator_id', $request->user()->id)->get();

        $operator = $request->user();

        switch ($operator->role) {
            case 'group_admin':
                return view('admins.group_admin.distributors-payments-download-create', [
                    'distributors' => $distributors,
                ]);
                break;

            case 'operator':
                return view('admins.operator.distributors-payments-download-create', [
                    'distributors' => $distributors,
                ]);
                break;

            case 'sub_operator':
                return view('admins.sub_operator.distributors-payments-download-create', [
                    'distributors' => $distributors,
                ]);
                break;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $where = [
            ['operator_id', '=', $request->user()->id],
        ];

        if ($request->filled('card_distributor_id')) {
            $where[] = ['card_distributor_id', '=', $request->card_distributor_id];
        }

        if ($request->filled('year')) {
            $where[] = ['year', '=', $request->year];
        }

        if ($request->filled('month')) {
            $where[] = ['month', '=', $request->month];
        }

        if ($request->filled('date')) {
            $where[] = ['date', '=', date_format(date_create($request->date), config('app.date_format'))];
        }

        $payments = card_distributor_payments::where($where)->get();

        if (count($payments) == 0) {
            return redirect()->route('distributor_payments.index')->with('success', 'Nothing to download');
        }

        $writer = SimpleExcelWriter::streamDownload('payments.xlsx');

        foreach ($payments as $payment) {

            $writer->addRow([
                'Distributor' => $payment->distributor->name,
                'amount paid' => $payment->amount_paid,
                'date' => $payment->date,
                'month' => $payment->month,
                'year' => $payment->year,
            ]);
        }

        $writer->toBrowser();
    }
}
