@component('mail::message')
# ListAdmin Update Notification

The MOMS Club of {{ $mailData['chapterNameUpd'] }}, {{ $mailData['chapterStateUpd'] }} has been updated through the MOMS Information Management Interface. Please update members of this chapter in any groups, forums, and mailing lists.

**MCL**,<br>
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
                <tr style="{{$mailData['borfname'] != $mailData['borfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['borfname']}}</td>
                    <td>{{$mailData['borfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['borlname'] != $mailData['borlnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['borlname']}}</td>
                    <td>{{$mailData['borlnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['boremail'] != $mailData['boremailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['boremail']}}</td>
                    <td>{{$mailData['boremailUpd']}}</td>
                </tr>
            </tbody>
        </table>
@endcomponent
<br>

@endcomponent

