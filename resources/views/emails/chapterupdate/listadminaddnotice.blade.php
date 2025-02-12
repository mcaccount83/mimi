@component('mail::message')
# ListAdmin Add Notification

The follownig chapter has added to MIMI:  MOMS Club of {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}}, Conference {{$mailData['conf']}}.<br>
<br>
Please add members of this chapter to any groups, forums and mailing lists.<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
<br>
@component('mail::table')
    <table>
        <tbody>
            <tr>
                <td></td>
                <td colspan="2" style="background-color: #D0D0D0;"><center><b>Board Information</b></center></td>
            </tr>
            <tr>
                <td>President</td>
                <td>{{$mailData['pfirst']}} {{$mailData['plast']}}</td>
                <td>{{$mailData['pemail']}}</td>
            </tr>
            <tr>
                <td>AVP</td>
                <td>{{$mailData['afirst']}} {{$mailData['alast']}}</td>
                <td>{{$mailData['aemail']}}</td>
            </tr>
            <tr>
                <td>MVP</td>
                <td>{{$mailData['mfirst']}} {{$mailData['mlast']}}</td>
                <td>{{$mailData['memail']}}</td>
            </tr>
            <tr>
                <td>Secretary</td>
                <td>{{$mailData['sfirst']}} {{$mailData['slast']}}</td>
                <td>{{$mailData['semail']}}</td>
            </tr>
            <tr>
                <td>Treasurer</td>
                <td>{{$mailData['tfirst']}} {{$mailData['tlast']}}</td>
                <td>{{$mailData['temail']}}</td>
            </tr>
        </tbody>
    </table>

@endcomponent
<br>

@endcomponent
