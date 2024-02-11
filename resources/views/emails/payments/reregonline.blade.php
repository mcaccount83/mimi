@component('mail::message')
# Re-Registration Payment Notification

The MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} has submitted their Re-Registration payment through the MOMS Information Management Interface.

<table>
    <tbody>
        <tr>
            <td>Payment Date:</td>
            <td>{{ $mailData['datePaid'] }}</td>
        </tr>
        <tr>
            <td>Number of Members:</td>
            <td>{{ $mailData['members'] }}</td>
        </tr>
        <tr>
            <td>Late Fee:</td>
            <td>{{ $mailData['late'] }}</td>
        </tr>
        <tr><strong>
            <td>Total Re-Registration Fees:</td>
            <td>{{ $mailData['reregTotal'] }}</td>
        </strong></tr>
        <tr>
            <td>Sustaining Chapter Donation:</td>
            <td>{{ $mailData['sustaining'] }}</td>
        </tr>
        <tr>
            <td>Online Processing Fee:</td>
            <td>{{ $mailData['processing'] }}</td>
        </tr>
        <tr><strong>
            <td>Total Paid:</td>
            <td>{{ $mailData['totalPaid'] }}</td>
        </strong></tr>
    </tbody>
</table>

<p>The Re-Registration Payment and Sustaining Chapter Donation (if they made one) have been entered into MIMI automatically, this is for notification purposes only.</p>

<strong>MCL,</strong><br>
MIMI Database Administrator
@endcomponent

