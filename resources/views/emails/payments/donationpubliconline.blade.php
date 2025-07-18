@component('mail::message')
# Admin Public Donation Notification

A public Donation to International MOMS Club. A Thank You email has been sent to the donor.<br>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
<br>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Donor Information</strong></center></td>
        </tr>
        <tr>
            <td>Company Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_company'] }}</td>
        </tr>
        <tr>
            <td>Donor Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_fname'] }} {{ $mailData['ship_lname'] }}</td>
        </tr>
        <tr>
            <td>Donar Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_email'] }}</td>
        </tr>
        <tr>
            <td>Donor Phone:&nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_phone'] }}</td>
        </tr>
        <tr>
            <td>Donor Address:&nbsp;&nbsp;<br>
                &nbsp;&nbsp;</td>
            <td>{{ $mailData['ship_street'] }}<br>
                {{ $mailData['ship_city'] }},
                {{ $mailData['ship_state'] }} {{ $mailData['ship_zip'] }}<br>
                {{ $mailData['ship_country']}}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Payment Information</strong></center></td>
        </tr>
        <tr>
            <td>Sustaining Chapter Donation:&nbsp;&nbsp;</td>
            <td>{{ $mailData['sustainingDonation'] }}</td>
        </tr>
        <tr>
            <td>Mother-to-Mother Fund Donation:&nbsp;&nbsp;</td>
            <td>{{ $mailData['m2mDonation'] }}</td>
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
            <td>{{ $mailData['invoice'] }}</td>
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

