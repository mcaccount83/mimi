@component('mail::message')
# Primary Coordinator Notification

The MOMS Club of  {{$mailData['chapterName']}}, {{$mailData['chapterState']}} has been updated through the MOMS Information Management Interface.<br>
<br>
<strong>MCL</strong>,<br>
MIMI Database Administrator
<br>

@component('mail::table')
       <table>
            <thead>
                <tr>
                    <th></th>
                    <th>Previous Information</th>
                    <th>Updated Information</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>President</strong></td>
                </tr>
                <tr style="{{$mailData['presNamePre'] != $mailData['presNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['presNamePre']}}</td>
                    <td>{{$mailData['presNameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presEmailPre'] != $mailData['presEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['presEmailPre']}}</td>
                    <td>{{$mailData['presEmailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presAddressPre'] != $mailData['presAddressUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Street</td>
                    <td>{{$mailData['presAddressPre']}}</td>
                    <td>{{$mailData['presAddressUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presCityPre'] != $mailData['presCityUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>City</td>
                    <td>{{$mailData['presCityPre']}}</td>
                    <td>{{$mailData['presCityUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presStatePre'] != $mailData['presStateUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>State</td>
                    <td>{{$mailData['presStatePre']}}</td>
                    <td>{{$mailData['presStateUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presZipPre'] != $mailData['presZipUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Zip</td>
                    <td>{{$mailData['presZipPre']}}</td>
                    <td>{{$mailData['presZipUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presPhpnePre'] != $mailData['presPhoneUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Phone</td>
                    <td>{{$mailData['presPhpnePre']}}</td>
                    <td>{{$mailData['presPhoneUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>AVP</strong></td>
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
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>MVP</strong></td>
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
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Treasurer</strong></td>
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
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Secretary</strong></td>
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
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Chapter Fields</strong></td>
                </tr>
                <tr style="{{$mailData['inquiriesContactPre'] != $mailData['inquiriesContactUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Contact</td>
                    <td>{{$mailData['inquiriesContactPre']}}</td>
                    <td>{{$mailData['inquiriesContactUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterEmailPre'] != $mailData['inquiriesNotesUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapterEmailPre']}}</td>
                    <td>{{$mailData['inquiriesNotesUpd']}}</td>
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
                        @if($mailData['websiteStatusPre']==1)
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
