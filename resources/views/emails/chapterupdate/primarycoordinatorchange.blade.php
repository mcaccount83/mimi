@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

Your chapter has been assigned a new Priamry Coordinator:
<br>
{{$mailData['name1']}} {{$mailData['name2']}}
{{$mailData['email1']}}
<br>
You should begin using her as your primary point of contact immediately.
<br>
You can view/update your chpater details as well as see your current Coordinator list at any time by logging into MIMI at https://momsclub.org/mimi.
<br>
Thanks,<br>
{{ config('app.name') }}
@endcomponent
