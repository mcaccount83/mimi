@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

Congratulations on a wonderful year!

After reviewing your End of Year reports, your chapter has been approved for the following awards.

@foreach($mailData['awardList'] as $award)
- **{{ $award['name'] }}**
@endforeach

Badges for the award(s) are also attached. Feel free to use these badges to display on your website
or other advertisements. You can also view/download award badges from your MIMI profile.

**MCL**,
{{ $mailData['userName'] }}
{{ $mailData['userPosition'] }}
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}
International MOMS Club
@endcomponent
