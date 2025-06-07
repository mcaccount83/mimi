@component('mail::message')
# New Chapter Application Notification

A New Chapter Application has been submitted for Conference {{ $mailData['chapterConf'] }}. Please review the application information and contact the founder to begin the Approval process.<br>
<br>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Application Information</strong></center></td>
        </tr>
         <tr>
            <td>Are you being sistered by another chapter?&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}</td>
        </tr>
         <tr>
            <td>If so, which chapter?&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}</td>
        </tr>
         <tr>
            <td>If not, how did you hear about us?&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}</td>
        </tr>
        <tr>
            <td>Requested Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}</td>
        </tr>
        <tr>
            <td>Requested Boundaries:&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterBoundaries'] }}</td>
        </tr>
        <tr>
            <td>Founder Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['founderName'] }}</td>
        </tr>
        <tr>
            <td>Founder Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['founderEmail'] }}</td>
        </tr>
        <tr>
            <td>Founder Phone:&nbsp;&nbsp;</td>
            <td>{{ $mailData['founderPhone'] }}</td>
        </tr>
         <tr>
            <td>Founder Address:&nbsp;&nbsp;</td>
            <td>{{ $mailData['founderAddress'] }}</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td>{{ $mailData['founderCity'] }}, {{ $mailData['founderState'] }} {{ $mailData['founderZip'] }}</td>
        </tr>
         <tr>
            <td>&nbsp;&nbsp;</td>
            <td>{{ $mailData['founderCountry'] }}</td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Payment Information</strong></center></td>
        </tr>
        <tr>
            <td>New Chapter Fee:&nbsp;&nbsp;</td>
            <td>{{ $mailData['newchap'] }}</td>
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
<br>
<p>The New Chapter Application Fee is authorize only and must be retrieved in 30 days or the founder will have to resubmit their application. New Chapter must be approved and moved
    from PENDING to ACTIVE status before fee can be retrieved.
</p>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

