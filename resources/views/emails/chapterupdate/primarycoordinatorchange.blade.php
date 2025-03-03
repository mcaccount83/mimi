@component('mail::message')
# MOMS Club of {{$mailData['chapterName']}}, {{$mailData['chapterState']}}

Your chapter has been assigned a new Priamry Coordinator:
<ul class="list-unstyled">
    <li>{{$mailData['pcNameUpd']}}</li>
    <li>{{$mailData['pcEmailUpd']}}</li>
</ul>
You should begin using her as your primary point of contact immediately.<br>
<br>
You can view/update your chpater details as well as see your current Coordinator list at any time by logging into MIMI at https://momsclub.org/mimi.<br>
<br>
<strong>MCL,</strong><br>
International MOMS Club
@endcomponent
