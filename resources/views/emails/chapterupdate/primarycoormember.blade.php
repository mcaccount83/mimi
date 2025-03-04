@component('mail::message')
# Primary Coordinator Notification

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
                <tr style="{{$mailData['borNamePre'] != $mailData['borNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['borNamePre']}}</td>
                    <td>{{$mailData['borNameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['borEmailPre'] != $mailData['borEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['borEmailPre']}}</td>
                    <td>{{$mailData['borEmailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Chapter Fields</strong></td>
                </tr>
                <tr style="{{$mailData['inquiriesContactPre'] != $mailData['inquiriesContactUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Contact</td>
                    <td>{{$mailData['inquiriesContactPre']}}</td>
                    <td>{{$mailData['inquiriesContactUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterEmailPre'] != $mailData['chapterEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapterEmailPre']}}</td>
                    <td>{{$mailData['chapterEmailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['poBoxPre'] != $mailData['poBoxUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>PO Box</td>
                    <td>{{$mailData['poBoxPre']}}</td>
                    <td>{{$mailData['poBoxUpd']}}</td>
                </tr>
                <tr style="{{$mailData['websiteURLPre'] != $mailData['websiteURLUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website URL</td>
                    <td>{{$mailData['websiteURLPre']}}</td>
                    <td>{{$mailData['websiteURLUpd']}}</td>
                </tr>
                <tr style="{{$mailData['websiteStatusPre'] != $mailData['websiteStatusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website Link Status</td>
                    <td>
                        @if($mailData['webStatusPre']==1)
                            Linked
                        @elseif($mailData['websiteStatusPre']==2)
                            Link Requested
                        @elseif($mailData['websiteStatusPre']==3)
                            Do Not Link
                        @else
                            Not Linked
                        @endif
                    </td>
                    <td>
                        @if($mailData['websiteStatusUpd']==1)
                            Linked
                        @elseif($mailData['websiteStatusUpd']==2)
                            Link Requested
                        @elseif($mailData['websiteStatusUpd']==3)
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
