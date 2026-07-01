@component('mail::message')
# Admin Public {{ $mailData['donationType'] ?? 'Donation' }} Notification

A public {{ $mailData['donationDescription'] ?? 'Donation' }} has been made to International MOMS Club.
A Thank You email has been sent to the donor.

If this is a donation from a chapter, please forward to the CC so the donation can be entered into
their MIMI profile.

**MCL,**
MIMI Database Administrator

---

<table style="width:100%; border-collapse: collapse; font-family: inherit; font-size: inherit;">
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Donor Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Company Name</td>
            <td style="padding: 8px;">{{ $mailData['ship_company'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Donor Name</td>
            <td style="padding: 8px;">{{ $mailData['ship_fname'] }} {{ $mailData['ship_lname'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Donor Email</td>
            <td style="padding: 8px;">{{ $mailData['ship_email'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Donor Phone</td>
            <td style="padding: 8px;">{{ $mailData['ship_phone'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Donor Address</td>
            <td style="padding: 8px;">
                {{ $mailData['ship_street'] }}<br>
                {{ $mailData['ship_city'] }}, {{ $mailData['ship_state'] }} {{ $mailData['ship_zip'] }}<br>
                {{ $mailData['ship_country'] }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Payment Information</strong></td>
        </tr>
        @if ($mailData['hasM2M'])
        <tr>
            <td style="padding: 8px;">Mother-to-Mother Fund Donation</td>
            <td style="padding: 8px;">{{ $mailData['m2mDonation'] }}</td>
        </tr>
        @endif
        @if ($mailData['hasSustaining'])
        <tr>
            <td style="padding: 8px;">Sustaining Chapter Donation</td>
            <td style="padding: 8px;">{{ $mailData['sustainingDonation'] }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 8px;">Online Processing Fee</td>
            <td style="padding: 8px;">{{ $mailData['processingFee'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Total Paid</td>
            <td style="padding: 8px;">{{ $mailData['totalPaid'] }}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Cardholder Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Invoice Number</td>
            <td style="padding: 8px;">{{ $mailData['invoice'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Cardholder Name</td>
            <td style="padding: 8px;">{{ $mailData['fname'] }} {{ $mailData['lname'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Cardholder Email</td>
            <td style="padding: 8px;">{{ $mailData['email'] }}</td>
        </tr>
    </tbody>
</table>
@endcomponent
