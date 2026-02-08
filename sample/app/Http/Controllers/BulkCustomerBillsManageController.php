<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Customer\CustomerBillGenerateController;
use App\Models\bulk_customer_bill_paid;
use App\Models\customer_bill;
use App\Models\Freeradius\customer;
use Illuminate\Http\Request;

class BulkCustomerBillsManageController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'verb' => 'in:delete,delete_and_generate,paid|required',
        ]);

        // function variables
        $bill_deleted = 0;

        // if has request
        if ($request->filled('bill_ids')) {

            // flush previous request
            bulk_customer_bill_paid::where('requester_id', $request->user()->id)->delete();

            // process request
            foreach ($request->bill_ids as $bill_id) {

                $bill = customer_bill::find($bill_id);

                switch ($request->verb) {
                    case 'delete':
                        if ($request->user()->can('deleteInvoice', $bill)) {
                            $controller = new CustomerBillController();
                            $controller->destroy($bill);
                            $bill_deleted++;
                        }
                        break;

                    case 'delete_and_generate':
                        if ($request->user()->can('deleteInvoice', $bill)) {
                            $customer = customer::find($bill->customer_id);
                            $controller = new CustomerBillController();
                            $controller->destroy($bill);
                            $bill_deleted++;
                            if ($customer) {
                                CustomerBillGenerateController::generateBill($customer);
                            }
                        }
                        break;

                    case 'paid':
                        if ($request->user()->can('receivePayment', $bill)) {
                            $entry = new bulk_customer_bill_paid();
                            $entry->requester_id = $request->user()->id;
                            $entry->customer_bill_id = $bill->id;
                            $entry->amount = $bill->amount;
                            $entry->operator_amount = $bill->operator_amount;
                            $entry->save();
                        }
                        break;
                }
            }
        }

        switch ($request->verb) {
            case 'delete':
                $msg = $bill_deleted . " bills have been deleted successfully!";
                return redirect()->route('customer_bills.index')->with('success', $msg);
                break;

            case 'delete_and_generate':
                $msg = $bill_deleted . " bills have been regenerated successfully!";
                return redirect()->route('customer_bills.index')->with('success', $msg);
                break;

            case 'paid':
                if (bulk_customer_bill_paid::where('requester_id', $request->user()->id)->count()) {
                    return redirect()->route('bulk_customer_bill_paids.create');
                } else {
                    return redirect()->route('customer_bills.index')->with('success', 'Noting to process!');
                }
                break;
        }
    }
}
