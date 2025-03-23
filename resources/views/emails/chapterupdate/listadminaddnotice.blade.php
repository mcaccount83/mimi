@component('mail::message')
# ListAdmin Add Notification

The follownig chapter has added to MIMI:  MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}, Conference {{$mailData['chapterConf']}}.<br>
<br>
Please add members of this chapter to any groups, forums and mailing lists.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>

@component('mail::table')
        <table>
            <tbody>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b> Board Information</b></center></td>
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
