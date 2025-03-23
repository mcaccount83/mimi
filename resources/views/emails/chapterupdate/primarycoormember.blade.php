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
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>{{ $mailData['borPosition'] }} Information</b></center></td>
                </tr>
                <tr style="{{$mailData['boardName'] != $mailData['borNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['boardName']}}</td>
                    <td>{{$mailData['borNameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['boardEmail'] != $mailData['borEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['boardEmail']}}</td>
                    <td>{{$mailData['borEmailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Chapter Fields</strong></td>
                </tr>
                <tr style="{{$mailData['chapterInquiriesContact'] != $mailData['inquiriesContactUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Contact</td>
                    <td>{{$mailData['chapterOnquiriesContact']}}</td>
                    <td>{{$mailData['inquiriesContactUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterEmail'] != $mailData['chapterEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapterEmail']}}</td>
                    <td>{{$mailData['chapterEmailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterPOBox'] != $mailData['poBoxUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>PO Box</td>
                    <td>{{$mailData['chapterPOBox']}}</td>
                    <td>{{$mailData['poBoxUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterWebsiteURL'] != $mailData['websiteURLUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website URL</td>
                    <td>{{$mailData['chapterWebsiteURL']}}</td>
                    <td>{{$mailData['websiteURLUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterWebsiteStatus'] != $mailData['websiteStatusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website Link Status</td>
                    <td>
                        @if($mailData['chapterWebsiteStatus']==1)
                            Linked
                        @elseif($mailData['chapterWebsiteStatus']==2)
                            Link Requested
                        @elseif($mailData['chapterWebsiteStatus']==3)
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
