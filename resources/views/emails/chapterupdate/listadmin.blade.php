@component('mail::message')
# ListAdmin Update Notification

Board member informationfor the MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface. Please update members of this chapter in any groups, forums, and mailing lists.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>

@component('mail::table')
        <table>
            <thead>
                <th></th>
                <th>Previous Information</th>
                <th>Updated Information</th>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>President</b></center></td>
                </tr>
                <tr style="{{$mailData['presName'] != $mailData['presNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['presName']}}</td>
                    <td>{{$mailData['presNameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presEmail'] != $mailData['presEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['presEmail']}}</td>
                    <td>{{$mailData['presEmailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>AVP</b></center></td>
                </tr>
                <tr style="{{$mailData['avpfname'] != $mailData['avpfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['avpfname']}}</td>
                    <td>{{$mailData['avpfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['avplname'] != $mailData['avplnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['avplname']}}</td>
                    <td>{{$mailData['avplnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['avpemail'] != $mailData['avpemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['avpemail']}}</td>
                    <td>{{$mailData['avpemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>MVP</b></center></td>
                </tr>
                <tr style="{{$mailData['mvpfname'] != $mailData['mvpfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['mvpfname']}}</td>
                    <td>{{$mailData['mvpfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['mvplname'] != $mailData['mvplnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['mvplname']}}</td>
                    <td>{{$mailData['mvplnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['mvpemail'] != $mailData['mvpemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['mvpemail']}}</td>
                    <td>{{$mailData['mvpemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>Treasurer</b></center></td>
                </tr>
                <tr style="{{$mailData['tresfname'] != $mailData['tresfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['tresfname']}}</td>
                    <td>{{$mailData['tresfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['treslname'] != $mailData['treslnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['treslname']}}</td>
                    <td>{{$mailData['treslnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['tresemail'] != $mailData['tresemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['tresemail']}}</td>
                    <td>{{$mailData['tresemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>Secretary</b></center></td>
                </tr>
                <tr style="{{$mailData['secfname'] != $mailData['secfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['secfname']}}</td>
                    <td>{{$mailData['secfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['seclname'] != $mailData['seclnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['seclname']}}</td>
                    <td>{{$mailData['seclnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['secemail'] != $mailData['secemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['secemail']}}</td>
                    <td>{{$mailData['secemailUpd']}}</td>
                </tr>
                <tr>
            </tbody>
        </table>
@endcomponent
<br>

@endcomponent
