@component('mail::message')
# Primary Coordinator Notification

Chapter Information for the MOMS Club of  {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}} has been updated through the MOMS Information Management Interface.<br>
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
                <tr style="{{$mailData['boundPre'] != $mailData['boundUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Boundaries</td>
                    <td>{{$mailData['boundPre']}}</td>
                    <td>{{$mailData['boundUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapstatusPre'] != $mailData['chapstatusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter Status</td>
                    <td>@if($mailData['chapstatusPre']==1) Operating Ok
                    @elseif($mailData['chapstatusPre']==4)
                    On Hold Do Not Refer
                    @elseif($mailData['chapstatusPre']==5)
                    Probation
                     @elseif($mailData['chapstatusPre']==6)
                     Probation Do Not Refer
                     @endif
                    </td>
                    <td>@if($mailData['chapstatusUpd']==1) Operating Ok
                    @elseif($mailData['chapstatusUpd']==4)
                    On Hold Do Not Refer
                    @elseif($mailData['chapstatusUpd']==5)
                    Probation
                     @elseif($mailData['chapstatusUpd']==6)
                     Probation Do Not Refer
                     @endif   </td>
                </tr>
                <tr style="{{$mailData['chapNotePre'] != $mailData['chapNoteUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Status Notes</td>
                    <td>{{$mailData['chapNotePre']}}</td>
                    <td>{{$mailData['chapNoteUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapemailPre'] != $mailData['chapemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapemailPre']}}</td>
                    <td>{{$mailData['chapemailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['poBoxPre'] != $mailData['poBoxUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>PO Box/Address</td>
                    <td>{{$mailData['poBoxPre']}}</td>
                    <td>{{$mailData['poBoxUpd']}}</td>
                </tr>
                <tr style="{{$mailData['inConPre'] != $mailData['inConUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Contact</td>
                    <td>{{$mailData['inConPre']}}</td>
                    <td>{{$mailData['inConUpd']}}</td>
                </tr>
                <tr style="{{$mailData['inNotePre'] != $mailData['inNoteUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Notes</td>
                    <td>{{$mailData['inNotePre']}}</td>
                    <td>{{$mailData['inNoteUpd']}}</td>
                </tr>
                <tr style="{{$mailData['addInfoPre'] != $mailData['addInfoUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Additional Information</td>
                    <td>{{$mailData['addInfoPre']}}</td>
                    <td>{{$mailData['addInfoUpd']}}</td>
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
                </tr>
                <tr style="{{($mailData['cor_fnamePre'] != $mailData['cor_fnameUpd']) && ($mailData['cor_lnamePre'] != $mailData['cor_lnameUpd']) ? 'background-color: yellow;' : ''}}">
                    <td>Primary Coordinator</td>
                    <td>{{$mailData['cor_fnamePre']}} {{$mailData['cor_lnamePre']}}</td>
                    <td>{{$mailData['cor_fnameUpd']}} {{$mailData['cor_lnameUpd']}}</td>
                </tr>
            </tbody>
        </table>
@endcomponent
<br>

@endcomponent
