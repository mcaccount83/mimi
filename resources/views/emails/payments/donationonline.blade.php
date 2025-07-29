@component('mail::message')
# Admin {{ $mailData['donationType'] ?? 'Donation' }} Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{$mailData['chapterState']}} has made a {{ $mailData['donationDescription'] ?? 'Donation' }}.<br>
<br>
The donation has been entered into MIMI and a Thank You email has been sent to the chapter.<br>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
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
                {{ $mailData['presCity'] }}, {{ $mailData['presState'] }} {{ $mailData['presZip'] }}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Payment Information</strong></center></td>
        </tr>
        @if ($mailData['hasM2M'])
            <tr>
                <td>Mother-to-Mother Fund Donation:&nbsp;&nbsp;</td>
                <td>{{ $mailData['m2mDonation'] }}</td>
            </tr>
        @endif
        @if ($mailData['hasSustaining'])
            <tr>
                <td>Sustaining Chapter Donation:&nbsp;&nbsp;</td>
                <td>{{ $mailData['sustainingDonation'] }}</td>
            </tr>
        @endif
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
            <td>{{ $mailData['donationInvoice'] }}</td>
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
@endcomponent
