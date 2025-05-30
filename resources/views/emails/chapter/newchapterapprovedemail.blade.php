@component('mail::message')
# New Chapter Notification

A New Chapter has been approved for Conference {{ $mailData['conference_id'] }}. Please create the following email address in Google GSuite. Once created, you'll need to go back and enter the new
email address into MIMI as the "chapter email" in their profile.<br>
<br>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Chapter Information</strong></center></td>
        </tr>
        <tr>
            <td>Name:&nbsp;&nbsp;</td>
            <td> {{$mailData['chapterName']}}, {{$mailData['chapterState']}}</td>
        </tr>
        <tr>
            <td>Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['chapterNameSanitized'] }}.{{$mailData['chapterState']}}@momsclub.org</td>
        </tr>
        <tr>
            <td>Secondary Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['presEmail'] }}</td>
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

