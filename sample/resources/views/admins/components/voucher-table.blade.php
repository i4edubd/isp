<table style="color:black;width:100%;border-collapse:collapse;">
    <tbody>
        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;"></td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;"></td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">Voucher ID</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">{{ $customer_payment->id }}</td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">User ID</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">
                {{ $customer_payment->customer_id }}</td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">User Name</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">{{ $customer_payment->name }}
            </td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">Description</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">{{ $package->name }}</td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">Validity</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">
                {{ $customer_payment->validity_period }}</td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">Amount Paid</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">
                {{ $customer_payment->amount_paid }} {{ getCurrency($customer_payment->operator_id) }}</td>
        </tr>
        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">Payment Date</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;font-size: 18px;">{{ $customer_payment->date }}
            </td>
        </tr>
    </tbody>
</table>
<!--/table -->
