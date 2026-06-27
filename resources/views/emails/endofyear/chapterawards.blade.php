@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

<p>Congratulations on a wonderful year!</p>
<br>
<p>After reviewing your End of Year reports, your chapter has been approved for the following awards.</p>
@component('mail::table')
@foreach($mailData['awardList'] as $award)
    **{{ $award['name'] }}**
@endforeach
@endcomponent
<p>Badges for the award(s) are also attached. Feel free to use these badges to display on your website
    or other advertisements. You can also view/download award badges from your MIMI profile.</p>
<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endcomponent
