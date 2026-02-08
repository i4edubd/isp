<?php

namespace App\Http\Controllers;

use App\Models\customer_bills_summary;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class CustomerBillsSummaryDownloadController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $operator = $request->user();

        $bills_summaries = customer_bills_summary::where('operator_id', $operator->id)->get();

        $file = 'bills-summary-' . date(config('app.date_format')) . '.xlsx';

        $writer = SimpleExcelWriter::streamDownload($file)->noHeaderRow();

        $style = (new StyleBuilder())
            ->setFontBold()
            ->setFontColor(Color::BLUE)
            ->setShouldWrapText()
            ->setBackgroundColor(Color::YELLOW)
            ->build();

        // Direct Selling
        $writer->addRow(['Business', 'From', 'Direct', 'Selling'], $style);

        $writer->addRow(['Package', 'Bill Count', 'Package Price', 'Subtotal']);

        foreach ($bills_summaries->where('type', 'direct') as  $DirectSelling) {
            $writer->addRow([
                $DirectSelling->package->name,
                $DirectSelling->bill_count,
                $DirectSelling->package_price,
                $DirectSelling->subtotal
            ]);
        }

        $writer->addRow(['', '', 'Total', $bills_summaries->where('type', 'direct')->sum('subtotal')]);

        $writer->addRow([]);

        // Business From Resellers
        if ($operator->role == 'group_admin' || $operator->role == 'operator') {

            $writer->addRow(['Business', 'From', 'Resellers'], $style);

            $writer->addRow(['Resellers', 'Package', 'Bill Count', 'Package Price', 'Subtotal']);

            foreach ($bills_summaries->where('type', 'resell')->groupBy('reseller_id') as $FromResellers) {

                foreach ($FromResellers as $FromReseller) {

                    $writer->addRow([
                        $FromReseller->reseller->id . '::' . $FromReseller->reseller->name,
                        $FromReseller->package->name,
                        $FromReseller->bill_count,
                        $FromReseller->package_price,
                        $FromReseller->subtotal
                    ]);
                }

                $writer->addRow(['', '', '', 'Total', $FromResellers->sum('subtotal')]);

                $writer->addRow([]);
            }

            $writer->addRow([]);
        }

        // Business From Sub-Resellers
        if ($operator->role == 'group_admin') {

            $writer->addRow(['Business', 'From', 'Sub-Resellers'], $style);

            $writer->addRow(['Resellers', 'Package', 'Bill Count', 'Package Price', 'Subtotal']);

            foreach ($bills_summaries->where('type', 'sub_resell')->groupBy('reseller_id') as $FromSubResellers) {

                foreach ($FromSubResellers as $FromSubReseller) {

                    $writer->addRow([
                        $FromSubReseller->reseller->id . "::" . $FromSubReseller->reseller->name . "(" . $FromSubReseller->sub_reseller->id . "::" . $FromSubReseller->sub_reseller->name . ")",
                        $FromSubReseller->package->name,
                        $FromSubReseller->bill_count,
                        $FromSubReseller->package_price,
                        $FromSubReseller->subtotal
                    ]);
                }

                $writer->addRow(['', '', '', 'Total', $FromSubResellers->sum('subtotal')]);

                $writer->addRow([]);
            }

            $writer->addRow([]);
        }

        // to_operator
        if ($operator->role == 'sub_operator') {

            $writer->addRow(['Payable', 'To', $operator->group_admin->name], $style);

            $writer->addRow(['Package', 'Bill Count', 'Package Price', 'Subtotal']);

            foreach ($bills_summaries->where('type', 'to_operator') as $to_operator) {
                $writer->addRow([
                    $to_operator->package->name,
                    $to_operator->bill_count,
                    $to_operator->package_price,
                    $to_operator->subtotal,
                ]);
            }
            $writer->addRow(['', '', 'Total', $bills_summaries->where('type', 'to_operator')->sum('subtotal')]);
            $writer->addRow([]);
        }

        // to_group_admin
        if ($operator->role == 'operator') {

            $writer->addRow(['Payable', 'To', $operator->group_admin->name], $style);

            $writer->addRow(['Package', 'Bill Count', 'Package Price', 'Subtotal']);

            foreach ($bills_summaries->where('type', 'to_group_admin') as $to_group_admin) {
                $writer->addRow([
                    $to_group_admin->package->name,
                    $to_group_admin->bill_count,
                    $to_group_admin->package_price,
                    $to_group_admin->subtotal
                ]);
            }

            $writer->addRow(['', '', 'Total', $bills_summaries->where('type', 'to_group_admin')->sum('subtotal')]);
        }

        $writer->toBrowser();
    }
}
