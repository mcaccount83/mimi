@component('mail::message')
# Re-Registration Admin Payment Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has submitted their
Re-Registration payment.

The Re-Registration Payment and Sustaining Chapter Donation (if they made one) have been entered into
MIMI and a Thank You email has been sent to the chapter.

**MCL,**
MIMI Database Administrator

---

<table style="width:100%; border-collapse: collapse; font-family: inherit; font-size: inherit;">
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Chapter Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Chapter Name</td>
            <td style="padding: 8px;">{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">President Name</td>
            <td style="padding: 8px;">{{ $mailData['presName'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">President Address</td>
            <td style="padding: 8px;">
                {{ $mailData['presAddress'] }}<br>
                {{ $mailData['presCity'] }}, {{ $mailData['presState'] }} {{ $mailData['presZip'] }}<br>
                {{ $mailData['presCountry'] }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Payment Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Payment Date</td>
            <td style="padding: 8px;">{{ $mailData['reregPaid'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Number of Members</td>
            <td style="padding: 8px;">{{ $mailData['reregMembers'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Late Fee</td>
            <td style="padding: 8px;">{{ $mailData['lateFee'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Total Re-Registration Fees</td>
            <td style="padding: 8px;">{{ $mailData['reregPayment'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Sustaining Chapter Donation</td>
            <td style="padding: 8px;">{{ $mailData['sustainingDonation'] }}</td>
        </tr>
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
            <td style="padding: 8px;">{{ $mailData['reregInvoice'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Customer ID</td>
            <td style="padding: 8px;">{{ $mailData['chapterId'] }}</td>
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
