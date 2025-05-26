@component('mail::message')
# New Coordinator Application Notification

A New Coordinator Application has been submitted for Conference {{ $mailData['conference_id'] }}. Please review the application information and contact the coordinator to get them started.<br>
<br>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Application Information</strong></center></td>
        </tr>
        <tr>
            <td>Volunteer Name:&nbsp;&nbsp;</td>
            <td>{{ $mailData['first_name'] }} {{$mailData['last_name']}}</td>
        </tr>
        <tr>
            <td>Email:&nbsp;&nbsp;</td>
            <td>{{ $mailData['email'] }}</td>
        </tr>
        <tr>
            <td>Phone:&nbsp;&nbsp;</td>
            <td>{{ $mailData['phone'] }}</td>
        </tr>
        <tr>
            <td>Home Chapter:&nbsp;&nbsp;</td>
            <td>{{ $mailData['home_chapter'] }}</td>
        </tr>
        <tr>
            <td>How long have you been a MOMS Club Member?&nbsp;&nbsp;</td>
            <td>{{ $mailData['start_date'] }}</td>
        </tr>
         <tr>
            <td>What jobs/offices have you held with the chapter? What programs/activities have you started or led?&nbsp;&nbsp;</td>
            <td>{{ $mailData['jobs_programs'] }}</td>
        </tr>
          <tr>
            <td>How has the MOMS Club helped you?&nbsp;&nbsp;</td>
            <td>{{ $mailData['helped_me'] }}</td>
        </tr>
         <tr>
            <td>Did you experience any problems during your time in the MOMS Club? If so, how were those problems resolved or what did you learn from them?&nbsp;&nbsp;</td>
            <td>{{ $mailData['problems'] }}</td>
        </tr>
         <tr>
            <td>Why do you want to be an International MOMS Club Volunteer?&nbsp;&nbsp;</td>
            <td>{{ $mailData['why_volunteer'] }}</td>
        </tr>
         <tr>
            <td>Do you volunteer for anyone else? Please list all your volunteer positions and when you did them?&nbsp;&nbsp;</td>
            <td>{{ $mailData['other_volunteer'] }}</td>
        </tr>
         <tr>
            <td>Do you have any special skills/talents/Hobbies (ie: other languages, proficient in any computer programs)?&nbsp;&nbsp;</td>
            <td>{{ $mailData['special_skills'] }}</td>
        </tr>
         <tr>
            <td>What have you enjoyed most in previous volunteer experiences? Least?&nbsp;&nbsp;</td>
            <td>{{ $mailData['enjoy_volunteering'] }}</td>
        </tr>
         <tr>
            <td>Referred by: (if applicable):&nbsp;&nbsp;</td>
            <td>{{ $mailData['referred_by'] }}</td>
        </tr>


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

