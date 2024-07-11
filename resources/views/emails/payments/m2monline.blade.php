@component('mail::message')
# M2M Admin Payment Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has made a Donation to the Mother-to-Mother Fund through the MOMS Information Management Interface.<br>
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
            <td>{{ $mailData['pres_fname'] }} {{ $mailData['pres_lname'] }}</td>
        </tr>
        <tr>
            <td>President Address:&nbsp;&nbsp;<br>
                &nbsp;&nbsp;</td>
            <td>{{ $mailData['pres_street'] }}<br>
                {{ $mailData['pres_city'] }}, {{ $mailData['pres_state'] }} {{ $mailData['pres_zip'] }}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Payment Information</strong></center></td>
        </tr>
        <tr>
            <td>Mother-to-Mother Fund Donation:&nbsp;&nbsp;</td>
            <td>{{ $mailData['donation'] }}</td>
        </tr>
        <tr>
            <td>Online Processing Fee:&nbsp;&nbsp;</td>
            <td>{{ $mailData['processing'] }}</td>
        </tr>
        <tr>
            <td>Total Paid:&nbsp;&nbsp;</td>
            <td>{{ $mailData['totalPaid'] }}</td>
        </tr>
        <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Cardholder Information</strong></center></td>
        </tr>
        <tr>
            <td>Invoice Number:&nbsp;&nbsp;</td>
            <td>{{ $mailData['invoice'] }}</td>
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
<p>The M2M Donation has been entered into MIMI automatically, this is for notification purposes only.</p>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

