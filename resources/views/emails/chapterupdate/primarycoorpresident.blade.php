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
                <tr style="{{$mailData['presName'] != $mailData['presNameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['presName']}}</td>
                    <td>{{$mailData['presNameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presEmail'] != $mailData['presEmailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['presEmail']}}</td>
                    <td>{{$mailData['presEmailUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presAddress'] != $mailData['presAddressUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Street</td>
                    <td>{{$mailData['presAddress']}}</td>
                    <td>{{$mailData['presAddressUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presCity'] != $mailData['presCityUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>City</td>
                    <td>{{$mailData['presCity']}}</td>
                    <td>{{$mailData['presCityUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presState'] != $mailData['presStateUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>State</td>
                    <td>{{$mailData['presState']}}</td>
                    <td>{{$mailData['presStateUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presZip'] != $mailData['presZipUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Zip</td>
                    <td>{{$mailData['presZip']}}</td>
                    <td>{{$mailData['presZipUpd']}}</td>
                </tr>
                <tr style="{{$mailData['presPhone'] != $mailData['presPhoneUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Phone</td>
                    <td>{{$mailData['presPhone']}}</td>
                    <td>{{$mailData['presPhoneUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>AVP</strong></td>
                </tr>
                <tr style="{{$mailData['avpfname'] != $mailData['avpfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['avpfname']}}</td>
                    <td>{{$mailData['avpfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['avplname'] != $mailData['avplnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['avplname']}}</td>
                    <td>{{$mailData['avplnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['avpemail'] != $mailData['avpemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['avpemail']}}</td>
                    <td>{{$mailData['avpemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>MVP</strong></td>
                </tr>
                <tr style="{{$mailData['mvpfname'] != $mailData['mvpfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['mvpfname']}}</td>
                    <td>{{$mailData['mvpfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['mvplname'] != $mailData['mvplnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['mvplname']}}</td>
                    <td>{{$mailData['mvplnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['mvpemail'] != $mailData['mvpemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['mvpemail']}}</td>
                    <td>{{$mailData['mvpemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Treasurer</strong></td>
                </tr>
                <tr style="{{$mailData['tresfname'] != $mailData['tresfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['tresfname']}}</td>
                    <td>{{$mailData['tresfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['treslname'] != $mailData['treslnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['treslname']}}</td>
                    <td>{{$mailData['treslnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['tresemail'] != $mailData['tresemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['tresemail']}}</td>
                    <td>{{$mailData['tresemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Secretary</strong></td>
                </tr>
                <tr style="{{$mailData['secfname'] != $mailData['secfnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>First Name</td>
                    <td>{{$mailData['secfname']}}</td>
                    <td>{{$mailData['secfnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['seclname'] != $mailData['seclnameUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Last Name</td>
                    <td>{{$mailData['seclname']}}</td>
                    <td>{{$mailData['seclnameUpd']}}</td>
                </tr>
                <tr style="{{$mailData['secemail'] != $mailData['secemailUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>E-mail</td>
                    <td>{{$mailData['secemail']}}</td>
                    <td>{{$mailData['secemailUpd']}}</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="background-color: #D0D0D0; text-align: center;"><strong>Chapter Fields</strong></td>
                </tr>
                <tr style="{{$mailData['chapterInquiriesContact'] != $mailData['inquiriesContactUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Inquiries Contact</td>
                    <td>{{$mailData['chapterInquiriesContact']}}</td>
                    <td>{{$mailData['inquiriesContactUpd']}}</td>
                </tr>
                <tr style="{{$mailData['chapterEmail'] != $mailData['inquiriesNotesUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Chapter E-mail</td>
                    <td>{{$mailData['chapterEmail']}}</td>
                    <td>{{$mailData['inquiriesNotesUpd']}}</td>
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
                <tr style="{{$mailData['chapterWebsiteURL'] != $mailData['websiteStatusUpd'] ? 'background-color: yellow;' : ''}}">
                    <td>Website Link Status</td>
                    <td>
                        @if($mailData['chapterWebsiteURL']==1)
                            Linked
                        @elseif($mailData['chapterWebsiteURL']==2)
                            Link Requested
                        @elseif($mailData['chapterWebsiteURL']==3)
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
