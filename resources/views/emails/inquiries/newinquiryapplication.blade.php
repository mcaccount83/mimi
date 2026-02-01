@component('mail::message')
# New Inquiry Notification

A potential member is inquiring about a chapter in your area.<br>
<ul>
    <li>{{ $mailData['state'] }}</li>
    <li>{{ $mailData['region'] }} Region</li>
    <li>Conference {{ $mailData['conf'] }}</li>
</ul>
<strong>MCL,</strong><br>
MIMI Database Administrator<br>
<br>
<table>
    <tbody>
        <tr>
            <td colspan="2" style="background-color: #D0D0D0;"><center><strong>Inquiry Information</strong></center></td>
        </tr>
         <tr>
            <td>Name&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryFirstName'] }} {{ $mailData['inquiryLastName'] }}</td>
        </tr>
        <tr>
            <td>Email&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryEmail'] }}</td>
        </tr>
        <tr>
            <td>Phone&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryPhone'] }}</td>
        </tr>
         <tr>
            <td>Address&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryAddress'] }}</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryCity'] }}, {{ $mailData['inquiryState'] }} {{ $mailData['inquiryZip'] }}</td>
        </tr>
         <tr>
            <td>&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryCountry'] }}</td>
        </tr>
          <tr>
            <td>County&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryCounty'] }}</td>
        </tr>
         @if (isset($mailData['inquiryTownship']) && !empty($mailData['inquiryTownship']))
            <tr>
                <td>Township&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryTownship'] }}</td>
            </tr>
        @endif
        @if (isset($mailData['inquiryArea']) && !empty($mailData['inquiryArea']))
            <tr>
                <td>Area&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryArea'] }}</td>
            </tr>
        @endif
         @if (isset($mailData['inquirySchool']) && !empty($mailData['inquirySchool']))
            <tr>
                <td>School District&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquirySchool'] }}</td>
            </tr>
        @endif
         @if (isset($mailData['inquiryComments']) && !empty($mailData['inquiryComments']))
            <tr>
                <td>Comments&nbsp;&nbsp;</td>
            <td>{{ $mailData['inquiryComments'] }}</td>
            </tr>
        @endif
    </tbody>
</table>
@endcomponent

