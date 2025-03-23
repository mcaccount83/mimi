@component('mail::message')
# Primary Coordinator Notification

Chapter Information for the MOMS Club of  {{$mailData['chapterNameUpd']}}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface.<br>
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
                <tr style="{{$mailData['chapterName'] != $mailData['chapterNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter Name</td>
                    <td>{{$mailData['chapterName']}}</td>
                    <td>{{$mailData['chapterNameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterBoundaries'] != $mailData['boundariesUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Boundaries</td>
                    <td>{{$mailData['chapterBoundaries']}}</td>
                    <td>{{$mailData['boundariesUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterStatus'] != $mailData['statusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter Status</td>
                    <td>@if($mailData['chapterStatus']==1) Operating Ok
                    @elseif($mailData['chapterStatus']==4)
                    On Hold Do Not Refer
                    @elseif($mailData['chapterStatus']==5)
                    Probation
                     @elseif($mailData['chapterStatus']==6)
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
                <tr style="{{$mailData['chapterNotes'] != $mailData['notesUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Status Notes</td>
                    <td>{{$mailData['chapterNotes']}}</td>
                    <td>{{$mailData['notesUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterEmail'] != $mailData['chapterEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapterEmail']}}</td>
                    <td>{{$mailData['chapterEmailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterPOBox'] != $mailData['poBoxUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>PO Box/Address</td>
                    <td>{{$mailData['chapterPOBox']}}</td>
                    <td>{{$mailData['poBoxUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterInquiriesContact'] != $mailData['inquiriesContactUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Contact</td>
                    <td>{{$mailData['chapterInquiriesContact']}}</td>
                    <td>{{$mailData['inquiriesContactUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterInquiriesNotes'] != $mailData['inquiriesNotesUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Notes</td>
                    <td>{{$mailData['chapterInquiriesNotes']}}</td>
                    <td>{{$mailData['inquiriesNotesUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterAdditionalInfo'] != $mailData['additionalInfoUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Additional Information</td>
                    <td>{{$mailData['chapterAdditionalInfo']}}</td>
                    <td>{{$mailData['additionalInfoUpd']}}</td>
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
                </tr>
                <tr style="{{$mailData['pcName'] != $mailData['pcNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Primary Coordinator</td>
                    <td>{{$mailData['pcName']}}</td>
                    <td>{{$mailData['pcNameUpd']}}</td>
                </tr>
            </tbody>
        </table>
@endcomponent
<br>

@endcomponent
