@component('mail::message')
# Admin Manual Order Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has ordered a replacement manual.

The order has been processed and a receipt has been sent to the chapter.

**MCL,**
MIMI Database Administrator

---

<table style="width:100%; border-collapse: collapse; font-family: inherit; font-size: inherit;">
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Shipping Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Chapter Name</td>
            <td style="padding: 8px;">{{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Shipping Name</td>
            <td style="padding: 8px;">{{ $mailData['ship_fname'] }} {{ $mailData['ship_lname'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Shipping Email</td>
            <td style="padding: 8px;">{{ $mailData['ship_email'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Shipping Phone</td>
            <td style="padding: 8px;">{{ $mailData['ship_phone'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Shipping Address</td>
            <td style="padding: 8px;">
                {{ $mailData['ship_street'] }}<br>
                {{ $mailData['ship_city'] }}, {{ $mailData['ship_state'] }} {{ $mailData['ship_zip'] }}<br>
                {{ $mailData['ship_country'] }}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Payment Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Chapter Manual</td>
            <td style="padding: 8px;">{{ $mailData['manualOrder'] }}</td>
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
            <td style="padding: 8px;">{{ $mailData['manualInvoice'] }}</td>
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
