@component('mail::message')
# ListAdmin Update Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has been updated through the MOMS Information Management Interface. Please update members of this chapter in any groups, forums, and mailing lists.<br>
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
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>{{ $mailData['borposition'] }} Information</b></center></td>
                </tr>
                <tr style="{{$mailData['borName'] != $mailData['borNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['borName']}}</td>
                    <td>{{$mailData['borNameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['borEmail'] != $mailData['borEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['borEmail']}}</td>
                    <td>{{$mailData['borEmailUpd']}}</td>
                </tr>
            </tbody>
        </table>
@endcomponent
<br>

@endcomponent

