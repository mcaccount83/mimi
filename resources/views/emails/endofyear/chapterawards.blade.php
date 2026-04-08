@if (empty($minimal) || $minimal == false)
    @component('mail::message')
@endif

@if (empty($minimal) || $minimal == false)
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

@endif
<p>Congratulations on a wonderful year!<br>
<br>
After reviewing your End of Year reports, your chapter has been approved for the following awards.
@component('mail::table')
@foreach($mailData['awardList'] as $award)
**{{ $award['name'] }}**
@endforeach
@endcomponent
Badges for the award(s) are also attached. Feel free to use these
badges to display on your website or other advertisements. You can also view/download award badges from your MIMI profile.<br>
@if (empty($minimal) || $minimal == false)
<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['userPosition'] }}<br>
    {{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}<br>
    International MOMS Club</p>
@endif
@if (empty($minimal) || $minimal == false)
    @endcomponent
@endif
