@component('mail::message')
# New Inquiry Notification

A potential member is inquiring about a chapter in your area.

- {{ $mailData['stateLong'] }}
- {{ $mailData['regionName'] }} Region
- Conference {{ $mailData['confDesc'] }}

**MCL,**
MIMI Database Administrator

---

<table style="width:100%; border-collapse: collapse; font-family: inherit; font-size: inherit;">
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0; padding: 8px; text-align: center;"><strong>Inquiry Information</strong></td>
        </tr>
        <tr>
            <td style="padding: 8px;">Name</td>
            <td style="padding: 8px;">{{ $mailData['inquiryFirstName'] }} {{ $mailData['inquiryLastName'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Email</td>
            <td style="padding: 8px;">{{ $mailData['inquiryEmail'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Phone</td>
            <td style="padding: 8px;">{{ $mailData['inquiryPhone'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">Address</td>
            <td style="padding: 8px;">{{ $mailData['inquiryAddress'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;"></td>
            <td style="padding: 8px;">{{ $mailData['inquiryCity'] }}, {{ $mailData['inquiryState'] }} {{ $mailData['inquiryZip'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;"></td>
            <td style="padding: 8px;">{{ $mailData['inquiryCountry'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px;">County</td>
            <td style="padding: 8px;">{{ $mailData['inquiryCounty'] }}</td>
        </tr>
        @if (isset($mailData['inquiryTownship']) && !empty($mailData['inquiryTownship']))
        <tr>
            <td style="padding: 8px;">Township</td>
            <td style="padding: 8px;">{{ $mailData['inquiryTownship'] }}</td>
        </tr>
        @endif
        @if (isset($mailData['inquiryArea']) && !empty($mailData['inquiryArea']))
        <tr>
            <td style="padding: 8px;">Area</td>
            <td style="padding: 8px;">{{ $mailData['inquiryArea'] }}</td>
        </tr>
        @endif
        @if (isset($mailData['inquirySchool']) && !empty($mailData['inquirySchool']))
        <tr>
            <td style="padding: 8px;">School District</td>
            <td style="padding: 8px;">{{ $mailData['inquirySchool'] }}</td>
        </tr>
        @endif
        @if (isset($mailData['inquiryComments']) && !empty($mailData['inquiryComments']))
        <tr>
            <td style="padding: 8px;">Comments</td>
            <td style="padding: 8px;">{{ $mailData['inquiryComments'] }}</td>
        </tr>
        @endif
    </tbody>
</table>
@endcomponent
