@component('mail::message')
# ListAdmin Disband Notification

The following chapter has disbanded:

MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}, Conference {{ $mailData['chapterConf'] }}.

Please remove members of this chapter from any groups, forums and mailing lists.

**MCL,**
International MOMS Club

<table style="width:100%; border-collapse: collapse; font-family: inherit; font-size: inherit;">
    <tbody>
        <tr>
            <td colspan="3" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Chapter Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Chapter Email</td>
            <td style="padding: 8px;" colspan="2">{{ $mailData['chapterEmail'] }}</td>
        </tr>
        <tr>
            <td colspan="3" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Board Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">President</td>
            <td style="padding: 8px;">{{ $mailData['presName'] }}</td>
            <td style="padding: 8px;">{{ $mailData['presEmail'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">AVP</td>
            <td style="padding: 8px;">{{ $mailData['avpName'] }}</td>
            <td style="padding: 8px;">{{ $mailData['avpEmail'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">MVP</td>
            <td style="padding: 8px;">{{ $mailData['mvpName'] }}</td>
            <td style="padding: 8px;">{{ $mailData['mvpEmail'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Treasurer</td>
            <td style="padding: 8px;">{{ $mailData['trsName'] }}</td>
            <td style="padding: 8px;">{{ $mailData['trsEmail'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Secretary</td>
            <td style="padding: 8px;">{{ $mailData['secName'] }}</td>
            <td style="padding: 8px;">{{ $mailData['secEmail'] }}</td>
        </tr>
    </tbody>
</table>
@endcomponent
