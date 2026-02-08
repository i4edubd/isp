<table style="font-size:12px;color:black;width:100%;border-collapse:collapse;">
    <tr>
        <th style="border-bottom: 1px solid #ddd;">Invoice ID</th>
        <th style="border-bottom: 1px solid #ddd;">User ID</th>
        <th style="border-bottom: 1px solid #ddd;">User Name</th>
        <th style="border-bottom: 1px solid #ddd;">Service</th>
        <th style="border-bottom: 1px solid #ddd;">Billing Period</th>
        <th style="border-bottom: 1px solid #ddd;">Amount</th>
        <th style="border-bottom: 1px solid #ddd;">Due Date</th>
    </tr>
    <tbody>

        @foreach ($bills as $bill)

        <tr>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;">{{ $bill->id }}</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;">{{ $bill->customer_id }}</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;">{{ $bill->username }}</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;">{{ $bill->description }}</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;">{{ $bill->billing_period }}</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;">{{ $bill->amount }} {{ config('consumer.currency') }}</td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;">{{ $bill->due_date }}</td>

        </tr>

        @endforeach

        <tr style="text-align:center">
            <td style="padding: 8px;"></td>
            <td style="padding: 8px;"></td>
            <td style="padding: 8px;"></td>
            <td style="padding: 8px;"></td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;"><i><b>Total Amount:</b></i></td>
            <td style="text-align:center;border-bottom: 1px solid #ddd;padding: 8px;">{{ $total_amount }} {{ config('consumer.currency') }}</td>
            <td></td>
        </tr>
    </tbody>
</table>
<!--/table -->
