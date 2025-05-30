@component('mail::message')
# New Chapter Notification

A New Chapter has been approved for Conference {{ $mailData['conference_id'] }}. Please capture their payment and mail their MOMS Club Manual.<br>
<br>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Chapter & Founder Information</strong></center></td>
        </tr>
        <tr>
            <td>Chapter Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterName'] }}, {{$mailData['chapterState']}}</td>
        </tr>
        <tr>
            <td>Founder Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['presName'] }}</td>
        </tr>
         <tr>
            <td>Address:&nbsp;&nbsp;</td>
            <td>{{ $mailData['presAddress'] }}</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td>{{ $mailData['presCity'] }}, {{ $mailData['presState'] }} {{ $mailData['presZip'] }}</td>
        </tr>
         <tr>
            <td>&nbsp;&nbsp;</td>
            <td>{{ $mailData['presCountry'] }}</td>
        </tr>
         <tr>
            <td>Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['presEmail'] }}</td>
        </tr>
        <tr>
            <td>Phone:&nbsp;&nbsp;</td>
            <td>{{ $mailData['presPhone'] }}</td>
        </tr>
    </tbody>
</table>
<br>
<p>
</p>
<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

