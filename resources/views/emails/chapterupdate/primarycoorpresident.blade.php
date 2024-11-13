@component('mail::message')
# Primary Coordinator Notification

The MOMS Club of  {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}} has been updated through the MOMS Information Management Interface.<br>
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
                    <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Chapter Email</strong></center></td>
                </tr>
                <tr style="{{$mailData['chapemailPre'] != $mailData['chapemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapemailPre']}}</td>
                    <td>{{$mailData['chapemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>President</b></center></td>
                </tr>
                <tr style="{{$mailData['chapfnamePre'] != $mailData['chapfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['chapfnamePre']}}</td>
                    <td>{{$mailData['chapfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chaplnamePre'] != $mailData['chaplnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['chaplnamePre']}}</td>
                    <td>{{$mailData['chaplnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapteremailPre'] != $mailData['chapteremailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['chapteremailPre']}}</td>
                    <td>{{$mailData['chapteremailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['streetPre'] != $mailData['streetUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Street</td>
                    <td>{{$mailData['streetPre']}}</td>
                    <td>{{$mailData['streetUpd']}}</td>
                </tr>
                <tr style="{{$mailData['cityPre'] != $mailData['cityUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>City</td>
                    <td>{{$mailData['cityPre']}}</td>
                    <td>{{$mailData['cityUpd']}}</td>
                </tr>
                <tr style="{{$mailData['statePre'] != $mailData['stateUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>State</td>
                    <td>{{$mailData['statePre']}}</td>
                    <td>{{$mailData['stateUpd']}}</td>
                </tr>
                <tr style="{{$mailData['zipPre'] != $mailData['zipUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Zip</td>
                    <td>{{$mailData['zipPre']}}</td>
                    <td>{{$mailData['zipUpd']}}</td>
                </tr>
                <tr style="{{$mailData['phonePre'] != $mailData['phoneUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Phone</td>
                    <td>{{$mailData['phonePre']}}</td>
                    <td>{{$mailData['phoneUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>AVP</b></center></td>
                </tr>
                <tr style="{{$mailData['avpfnamePre'] != $mailData['avpfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['avpfnamePre']}}</td>
                    <td>{{$mailData['avpfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['avplnamePre'] != $mailData['avplnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['avplnamePre']}}</td>
                    <td>{{$mailData['avplnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['avpemailPre'] != $mailData['avpemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['avpemailPre']}}</td>
                    <td>{{$mailData['avpemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>MVP</b></center></td>
                </tr>
                <tr style="{{$mailData['mvpfnamePre'] != $mailData['mvpfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['mvpfnamePre']}}</td>
                    <td>{{$mailData['mvpfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['mvplnamePre'] != $mailData['mvplnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['mvplnamePre']}}</td>
                    <td>{{$mailData['mvplnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['mvpemailPre'] != $mailData['mvpemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['mvpemailPre']}}</td>
                    <td>{{$mailData['mvpemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>Treasurer</b></center></td>
                </tr>
                <tr style="{{$mailData['tresfnamePre'] != $mailData['tresfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['tresfnamePre']}}</td>
                    <td>{{$mailData['tresfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['treslnamePre'] != $mailData['treslnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['treslnamePre']}}</td>
                    <td>{{$mailData['treslnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['tresemailPre'] != $mailData['tresemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['tresemailPre']}}</td>
                    <td>{{$mailData['tresemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>Secretary</b></center></td>
                </tr>
                <tr style="{{$mailData['secfnamePre'] != $mailData['secfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['secfnamePre']}}</td>
                    <td>{{$mailData['secfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['seclnamePre'] != $mailData['seclnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['seclnamePre']}}</td>
                    <td>{{$mailData['seclnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['secemailPre'] != $mailData['secemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['secemailPre']}}</td>
                    <td>{{$mailData['secemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0;"><center><b>Chapter Fields</b></center></td>
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
                <tr style="{{$mailData['egroupPre'] != $mailData['egroupUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-Group</td>
                    <td>{{$mailData['egroupPre']}}</td>
                    <td>{{$mailData['egroupUpd']}}</td>
                </tr>

            </tbody>
        </table>
@endcomponent
<br>

@endcomponent
