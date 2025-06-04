@component('mail::message')
# New Coordinator GSuite Admin Notification

A New Coordinator has been approved. Please create the following email address in Google GSuite.  If you choose to create an email address
different than what is listed below, you will need to go back and update MIMI.  Otherwise, MIMI is already set up with the one listed here.<br>
<br>

<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Coordinator Information</strong></center></td>
        </tr>
        <tr>
            <td>Conference:&nbsp;&nbsp;</td>
            <td>{{ $mailData['conference_id'] }}</td>
        </tr>
        <tr>
            <td>Region:&nbsp;&nbsp;</td>
            <td>{{ $mailData['region'] }}</td>
        </tr>
        <tr>
            <td>Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['first_name'] }} {{$mailData['last_name']}}</td>
        </tr>
        <tr>
            <td>MOMS Club Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['email'] }}</td>
        </tr>
        <tr>
            <td>Secondary Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['sec_email'] }}</td>
        </tr>
    </tbody>
</table>

<br>
<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

