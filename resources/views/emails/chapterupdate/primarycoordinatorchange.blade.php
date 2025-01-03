@component('mail::message')
# MOMS Club of {{$mailData['chapter_name']}}, {{$mailData['chapter_state']}}

Your chapter has been assigned a new Priamry Coordinator:
<ul class="list-unstyled">
    <li>{{$mailData['name1']}} {{$mailData['name2']}}</li>
    <li>{{$mailData['email1']}}</li>
</ul>
You should begin using her as your primary point of contact immediately.<br>
<br>
You can view/update your chpater details as well as see your current Coordinator list at any time by logging into MIMI at https://momsclub.org/mimi.<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
