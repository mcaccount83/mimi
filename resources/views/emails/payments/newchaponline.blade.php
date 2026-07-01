@component('mail::message')
# New Chapter Application Notification

A new Chapter Application has been submitted for Conference {{ $mailData['chapterConf'] }}. Please review
the application information and contact the founder to begin the approval process.

The New Chapter Application Fee is authorize only and must be retrieved in 30 days or the founder will
have to resubmit their application. New Chapter must be approved and moved from PENDING to ACTIVE status
before fee can be retrieved.

**MCL,**
MIMI Database Administrator

---

<table style="width:100%; border-collapse: collapse; font-family: inherit; font-size: inherit;">
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Application Information</strong></td>
        </tr>
        @if (isset($mailData['sistered_by']) && !empty($mailData['sistered_by']))
        <tr>
            <td style="padding: 8px;">Are you being sistered by another chapter?</td>
            <td style="padding: 8px;">YES</td>
        </tr>
        <tr>
            <td style="padding: 8px;">If so, which chapter?</td>
            <td style="padding: 8px;">{{ $mailData['sistered_by'] }}</td>
        </tr>
        @endif
        @if (isset($mailData['hear_about']) && !empty($mailData['hear_about']))
        <tr>
            <td style="padding: 8px;">Are you being sistered by another chapter?</td>
            <td style="padding: 8px;">NO</td>
        </tr>
        <tr>
            <td style="padding: 8px;">If not, how did you hear about us?</td>
            <td style="padding: 8px;">{{ $mailData['hear_about'] }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding: 8px;">Requested Name</td>
            <td style="padding: 8px;">{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Requested Boundaries</td>
            <td style="padding: 8px;">{{ $mailData['chapterBoundaries'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Founder Name</td>
            <td style="padding: 8px;">{{ $mailData['founderName'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Founder Email</td>
            <td style="padding: 8px;">{{ $mailData['founderEmail'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Founder Phone</td>
            <td style="padding: 8px;">{{ $mailData['founderPhone'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Founder Address</td>
            <td style="padding: 8px;">{{ $mailData['founderAddress'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;"></td>
            <td style="padding: 8px;">{{ $mailData['founderCity'] }}, {{ $mailData['founderState'] }} {{ $mailData['founderZip'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;"></td>
            <td style="padding: 8px;">{{ $mailData['founderCountry'] }}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Payment Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">New Chapter Fee</td>
            <td style="padding: 8px;">{{ $mailData['newchap'] }}</td>
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
            <td style="padding: 8px;">Payment Type</td>
            <td style="padding: 8px;">{{ $mailData['paymentType'] }}</td>
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
