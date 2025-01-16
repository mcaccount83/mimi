@component('mail::message')
# Primary Coordinator Notification

The MOMS Club of {{ $mailData['chapter_name'] }}, {{ $mailData['chapter_state'] }} has been updated through the MOMS Information Management Interface. Please update members of this chapter in any groups, forums, and mailing lists.<br>
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
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Chapter Fields</strong></td>
                </tr>
                <tr style="{{$mailData['inConPre'] != $mailData['inConUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Contact</td>
                    <td>{{$mailData['inConPre']}}</td>
                    <td>{{$mailData['inConUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapemailPre'] != $mailData['chapemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapemailPre']}}</td>
                    <td>{{$mailData['chapemailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['poBoxPre'] != $mailData['poBoxUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>PO Box</td>
                    <td>{{$mailData['poBoxPre']}}</td>
                    <td>{{$mailData['poBoxUpd']}}</td>
                </tr>
                <tr style="{{$mailData['webUrlPre'] != $mailData['webUrlUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website URL</td>
                    <td>{{$mailData['webUrlPre']}}</td>
                    <td>{{$mailData['webUrlUpd']}}</td>
                </tr>
                <tr style="{{$mailData['webStatusPre'] != $mailData['webStatusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website Link Status</td>
                    <td>
                        @if($mailData['webStatusPre']==1)
                            Linked
                        @elseif($mailData['webStatusPre']==2)
                            Link Requested
                        @elseif($mailData['webStatusPre']==3)
                            Do Not Link
                        @else
                            Not Linked
                        @endif
                    </td>
                    <td>
                        @if($mailData['webStatusUpd']==1)
                            Linked
                        @elseif($mailData['webStatusUpd']==2)
                            Link Requested
                        @elseif($mailData['webStatusUpd']==3)
                            Do Not Link
                        @else
                            Not Linked
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
@endcomponent
<br>

@endcomponent
