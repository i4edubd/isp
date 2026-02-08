@component('mail::message')
# Dear Concern,

New software subscription payment received as follows:

@component('mail::table')
| Admin ID | {{ $subscription_payment->gid }} |
|:---------|:---------------------------------|
| Admin Name | {{ $subscription_payment->operator_name }} |
| Amount Paid | {{ $subscription_payment->amount_paid }} |
| Transaction Fee | {{ $subscription_payment->transaction_fee }} |
| Store Amount | {{ $subscription_payment->store_amount }} |
| Txn ID | {{ $subscription_payment->mer_txnid }} |
| PGW Txn ID | {{ $subscription_payment->pgw_txnid }} |
| Bank Txn ID | {{ $subscription_payment->bank_txnid }} |
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
