@component('mail::message')
# Primary Coordinator Notification

Website Information for the MOMS Club of  {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}} has been updated through the MOMS Information Management Interface.<br>
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
                <tr style="{{$mailData['webUrlPre'] != $mailData['webUrlUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website URL</td>
                    <td>{{$mailData['webUrlPre']}}</td>
                    <td>{{$mailData['webUrlUpd']}}</td>
                </tr>
                <tr style="{{$mailData['webStatusPre'] != $mailData['webStatusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website Link Status</td>
                    <td>@if($mailData['webStatusPre']==1)
                            Linked
                            @elseif($mailData['webStatusPre']==2)
                            Link Requested
                            @elseif($mailData['webStatusPre']==3)
                            Do Not Link
                            @else
                            Not Linked
                            @endif</td>
                    <td>@if($mailData['webStatusUpd']==1)
                        Linked
                        @elseif($mailData['webStatusUpd']==2)
                        Link Requested
                        @elseif($mailData['webStatusUpd']==3)
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
