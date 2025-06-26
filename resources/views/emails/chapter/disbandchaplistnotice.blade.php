@component('mail::message')
# ListAdmin Disband Notification

The follownig chapter has disbanded:<br>
<br>
MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}, Conference {{$mailData['chapterConf']}}.<br>
<br>
Please remove members of this chapter from any groups, forums and mailing lists.<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
 <br>
 @component('mail::table')
 <table>
    <tbody>
        <tr>
            <td></td>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Chapter Information</strong></center></td>
        </tr>
        <tr>
            <td>Chapter Email</td>
            <td>{{$mailData['chapterEmail']}}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" style="background-color: #D0D0D0;"><center><b>Board Information</b></center></td>
        </tr>
        <tr>
            <td>President</td>
            <td>{{$mailData['presName']}}</td>
            <td>{{$mailData['presEmail']}}</td>
        </tr>
        <tr>
            <td>AVP</td>
            <td>{{$mailData['avpName']}}</td>
            <td>{{$mailData['avpEmail']}}</td>
        </tr>
        <tr>
            <td>MVP</td>
            <td>{{$mailData['mvpName']}}</td>
            <td>{{$mailData['mvpEmail']}}</td>
        </tr>
        <tr>
            <td>Treasurer</td>
            <td>{{$mailData['trsName']}}</td>
            <td>{{$mailData['trsEmail']}}</td>
        </tr>
        <tr>
            <td>Secretary</td>
            <td>{{$mailData['secName']}}</td>
            <td>{{$mailData['secEmail']}}</td>
        </tr>
    </tbody>
</table>
@endcomponent
<br>

@endcomponent
