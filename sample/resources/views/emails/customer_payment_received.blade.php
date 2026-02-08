@component('mail::message')
# Dear Concern,

New Customer Payment received as follows:

@component('mail::table')
| Customer ID | {{ $customer_payment->customer_id }} |
|:---------|:---------------------------------|
| Customer Name | {{ $customer_payment->name }} |
| Customer Username | {{ $customer_payment->username }} |
| Customer Mobile | {{ $customer_payment->mobile }} |
| Payment Gateway | {{ $customer_payment->payment_gateway_name }} |
| Amount Paid | {{ $customer_payment->amount_paid }} |
| Transaction Fee | {{ $customer_payment->transaction_fee }} |
| Store Amount | {{ $customer_payment->store_amount }} |
| Txn ID | {{ $customer_payment->mer_txnid }} |
| PGW Txn ID | {{ $customer_payment->pgw_txnid }} |
| Bank Txn ID | {{ $customer_payment->bank_txnid }} |
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
