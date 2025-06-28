@component('mail::message')
# Re-Registration Admin Payment Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has submitted their Re-Registration payment.<br>
<br>
The Re-Registration Payment and Sustaining Chapter Donation (if they made one) have been entered into MIMI and a Thank You email has been sent to the chapter.<br>
<br>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Chapter Information</strong></center></td>
        </tr>
        <tr>
            <td>Chapter Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}</td>
        </tr>
        <tr>
            <td>President Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['presName'] }}</td>
        </tr>
        <tr>
            <td>President Address:&nbsp;&nbsp;<br>
                &nbsp;&nbsp;</td>
            <td>{{ $mailData['presAddress'] }}<br>
                {{ $mailData['presCity'] }}, {{ $mailData['presState'] }} {{ $mailData['presZip'] }}<br>
                {{ $mailData['presCountry']}}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Payment Information</strong></center></td>
        </tr>
        <tr>
            <td>Payment Date:&nbsp;&nbsp;</td>
            <td>{{ $mailData['reregPaid'] }}</td>
        </tr>
        <tr>
            <td>Number of Members:&nbsp;&nbsp;</td>
            <td>{{ $mailData['reregMembers'] }}</td>
        </tr>
        <tr>
            <td>Late Fee:&nbsp;&nbsp;</td>
            <td>{{ $mailData['lateFee'] }}</td>
        </tr>
        <tr>
            <td>Total Re-Registration Fees:&nbsp;&nbsp;</td>
            <td>{{ $mailData['reregPayment'] }}</td>
        </tr>
        <tr>
            <td>Sustaining Chapter Donation:&nbsp;&nbsp;</td>
            <td>{{ $mailData['sustainingDonation'] }}</td>
        </tr>
        <tr>
            <td>Online Processing Fee:&nbsp;&nbsp;</td>
            <td>{{ $mailData['processingFee'] }}</td>
        </tr>
        <tr>
            <td>Total Paid:&nbsp;&nbsp;</td>
            <td>{{ $mailData['totalPaid'] }}</td>
        </tr>
        <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Cardholder Information</strong></center></td>
        </tr>
        <tr>
            <td>Invoice Number:&nbsp;&nbsp;</td>
            <td>{{ $mailData['reregInvoice'] }}</td>
        </tr>
        <tr>
            <td>Customer ID:&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterId'] }}</td>
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
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

