@component('mail::message')
# Primary Coordinator Notification

Chapter Information for the MOMS Club of  {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface.<br>
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
                    <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Chapter Information</strong></center></td>
                </tr>
                </tr>
                <tr style="{{$mailData['chapterNamePre'] != $mailData['chapterNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter Name</td>
                    <td>{{$mailData['chapterNamePre']}}</td>
                    <td>{{$mailData['chapterNameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['boundariesPre'] != $mailData['boundariesUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Boundaries</td>
                    <td>{{$mailData['boundariesPre']}}</td>
                    <td>{{$mailData['boundariesUpd']}}</td>
                </tr>
                <tr style="{{$mailData['statusPre'] != $mailData['statusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter Status</td>
                    <td>@if($mailData['statusPre']==1) Operating Ok
                    @elseif($mailData['statusPre']==4)
                    On Hold Do Not Refer
                    @elseif($mailData['statusPre']==5)
                    Probation
                     @elseif($mailData['statusPre']==6)
                     Probation Do Not Refer
                     @endif
                    </td>
                    <td>@if($mailData['statusUpd']==1) Operating Ok
                    @elseif($mailData['statusUpd']==4)
                    On Hold Do Not Refer
                    @elseif($mailData['statusUpd']==5)
                    Probation
                     @elseif($mailData['statusUpd']==6)
                     Probation Do Not Refer
                     @endif   </td>
                </tr>
                <tr style="{{$mailData['notesPre'] != $mailData['notesUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Status Notes</td>
                    <td>{{$mailData['notesPre']}}</td>
                    <td>{{$mailData['notesUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterEmailPre'] != $mailData['chapterEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapterEmailPre']}}</td>
                    <td>{{$mailData['chapterEmailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['poBoxPre'] != $mailData['poBoxUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>PO Box/Address</td>
                    <td>{{$mailData['poBoxPre']}}</td>
                    <td>{{$mailData['poBoxUpd']}}</td>
                </tr>
                <tr style="{{$mailData['inquiriesContactPre'] != $mailData['inquiriesContactUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Contact</td>
                    <td>{{$mailData['inquiriesContactPre']}}</td>
                    <td>{{$mailData['inquiriesContactUpd']}}</td>
                </tr>
                <tr style="{{$mailData['inquiriesNotesPre'] != $mailData['inquiriesNotesUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Notes</td>
                    <td>{{$mailData['inquiriesNotesPre']}}</td>
                    <td>{{$mailData['inquiriesNotesUpd']}}</td>
                </tr>
                <tr style="{{$mailData['additionalInfoPre'] != $mailData['additionalInfoUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Additional Information</td>
                    <td>{{$mailData['additionalInfoPre']}}</td>
                    <td>{{$mailData['additionalInfoUpd']}}</td>
                </tr>
                <tr style="{{$mailData['websiteURLPre'] != $mailData['websiteURLUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website URL</td>
                    <td>{{$mailData['websiteURLPre']}}</td>
                    <td>{{$mailData['websiteURLUpd']}}</td>
                </tr>
                <tr style="{{$mailData['websiteStatusPre'] != $mailData['websiteStatusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website Link Status</td>
                    <td>@if($mailData['websiteStatusPre']==1)
                            Linked
                            @elseif($mailData['websiteStatusPre']==2)
                            Link Requested
                            @elseif($mailData['websiteStatusPre']==3)
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
                </tr>
                <tr style="{{$mailData['pcNamePre'] != $mailData['pcNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Primary Coordinator</td>
                    <td>{{$mailData['pcNamePre']}}</td>
                    <td>{{$mailData['pcNameUpd']}}</td>
                </tr>
            </tbody>
        </table>
@endcomponent
<br>

@endcomponent
