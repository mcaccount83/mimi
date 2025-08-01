@if (empty($minimal) || $minimal === false)
    @component('mail::message')

# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

@endif
<p>Don't forget to complete the Financial Report for your chapter!  This report is available now and is due no later than July 15th at 11:59pm.<br>
<br>
After receiving your completed reports, your Coordiantor Team will review the report and reach out if they have any questions.<br>
<br>
The Financial Report (as well as the Board Election Report) can be accessed by logging into your MIMI account https://momsclub.org/mimi and selecting the buttons on the right hand side of your screen.</p>
@if (empty($minimal) || $minimal === false)
    <br>
    <strong>MCL,</strong><br>
    MIMI Database Administrator

    @endcomponent
@endif
