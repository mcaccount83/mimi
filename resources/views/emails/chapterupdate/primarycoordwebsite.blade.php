@component('mail::message')
# Primary Coordinator Notification

Website Information for the MOMS Club of  {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface.<br>
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
                    <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Website Information</strong></center></td>
                </tr>
                </tr>
                <tr style="{{$mailData['chapterWebsiteURL'] != $mailData['websiteURLUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website URL</td>
                    <td>{{$mailData['chapterWebsiteURL']}}</td>
                    <td>{{$mailData['websiteURLUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterWebsiteStatus'] != $mailData['websiteStatusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website Link Status</td>
                    <td>@if($mailData['chapterWebsiteStatus']==1)
                            Linked
                            @elseif($mailData['chapterWebsiteStatus']==2)
                            Link Requested
                            @elseif($mailData['chapterWebsiteStatus']==3)
                            Do Not Link
                            @else
                            Not Linked
                            @endif</td>
                    <td>@if($mailData['websiteStatusUpd']==1)
                        Linked
                        @elseif($mailData['websiteStatusUpd']==2)
                        Link Requested
                        @elseif($mailData['websiteStatusUpd']==3)
                        Do Not Link
                        @else
                        Not Linked
                    @endif</td>
                </td>
                    </td>
                </tr>

            </tbody>
        </table>
@endcomponent
<br>

@endcomponent
