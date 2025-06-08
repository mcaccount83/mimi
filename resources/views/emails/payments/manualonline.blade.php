@component('mail::message')
# Admin Manual Order Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has ordered a replacement manual through the MOMS Information Management Interface.<br>
<br>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Shipping Information</strong></center></td>
        </tr>
        <tr>
            <td>Chapter Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}</td>
        </tr>
        <tr>
            <td>Shipping Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_fname'] }} {{ $mailData['ship_lname'] }}</td>
        </tr>
        <tr>
            <td>Shipping Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_email'] }} </td>
        </tr>
        <tr>
            <td>Shipping Phone:&nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_phone'] }} </td>
        </tr>
        <tr>
            <td>Shipping Address:&nbsp;&nbsp;<br>
                &nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_street'] }}<br>
                {{ $mailData['ship_city'] }}, {{ $mailData['ship_state'] }} {{ $mailData['ship_zip'] }}<br>
                {{ $mailData['ship_country']}}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Payment Information</strong></center></td>
        </tr>
        <tr>
            <td>Chapter Manual:&nbsp;&nbsp;</td>
            <td>{{ $mailData['manualOrder'] }}</td>
        </tr>
        <tr>
            <td>Online Processing Fee:&nbsp;&nbsp;</td>
            <td>{{ $mailData['processingFee'] }}</td>
        </tr>
        <tr>
            <td>Total Paid:&nbsp;&nbsp;</td>
            <td>{{ $mailData['totalPaid'] }}</td>
        </tr>
        <tr>
            <td>Payment Type:&nbsp;&nbsp;</td>
            <td>{{ $mailData['paymentType'] }}</td>
        </tr>
        <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Cardholder Information</strong></center></td>
        </tr>
        <tr>
            <td>Invoice Number:&nbsp;&nbsp;</td>
            <td>{{ $mailData['manualInvoice'] }}</td>
        </tr>
        <tr>
            <td>Cardholder Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['fname'] }} {{ $mailData['lname'] }}</td>
        </tr>
        <tr>
            <td>Cardholder Email:&nbsp;&nbsp;</td>
        <td>{{ $mailData['email'] }}</td>
    </tbody>
</table>
<br>
<p>The manual has been ordered, this is for notification purposes only.</p>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

