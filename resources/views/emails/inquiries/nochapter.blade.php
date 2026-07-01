@component('mail::message')
**{{ $mailData['inquiryFirstName'] }},**

Thanks for your interest in MOMS Club. I am sorry there is not a chapter in your area. If you are
interested in starting one, we would LOVE to help!

I know the idea of starting a new chapter can be intimidating but it is actually really easy.
If you'd like more information – there is information on our website at
[https://momsclub.org/chapters](https://momsclub.org/chapters), and the direct application
is [https://momsclub.org/mimi/newchapter](https://momsclub.org/mimi/newchapter). But,
if you have any questions or just want more information, please feel free to ask.

When you register a MOMS Club, you receive a MOMS Club manual. This manual helps you through
getting your chapter started. You also receive a MOMS Club Coordinator. This is a person who is
there to help whenever you need it.

Should you decide that starting one is not for you, please check back from time to time as we do have
new chapters starting all the time!

**MCL,**
{{ $mailData['userName'] }}
{{ $mailData['userPosition'] }}
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}
International MOMS Club
@endcomponent
