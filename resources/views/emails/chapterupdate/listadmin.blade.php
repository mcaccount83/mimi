@component('mail::message')
# MOMS Club

The MOMS Club of {{ $chapterNameUpd }}, {{ $chapterStateUpd }} has been updated through the MOMS Information Management Interface. Please update members of this chapter in any groups, forums, and mailing lists.

@component('mail::table')
|                  | Previous Information | Updated Information |
|------------------|----------------------|----------------------|
| **Chapter Email**|                      |                      |
| Chapter E-mail   | {{ $chapemailPre }}  | {{ $chapemailUpd }}  |
| **President**    |                      |                      |
| First Name       | {{ $chapfnamePre }}  | {{ $chapfnameUpd }}  |
| Last Name        | {{ $chaplnamePre }}  | {{ $chaplnameUpd }}  |
| E-mail           | {{ $chapteremailPre }}| {{ $chapteremailUpd }}|
| **AVP**          |                      |                      |
| First Name       | {{ $avpfnamePre }}   | {{ $avpfnameUpd }}   |
| Last Name        | {{ $avplnamePre }}   | {{ $avplnameUpd }}   |
| E-mail           | {{ $avpemailPre }}   | {{ $avpemailUpd }}   |
| **MVP**          |                      |                      |
| First Name       | {{ $mvpfnamePre }}   | {{ $mvpfnameUpd }}   |
| Last Name        | {{ $mvplnamePre }}   | {{ $mvplnameUpd }}   |
| E-mail           | {{ $mvpemailPre }}   | {{ $mvpemailUpd }}   |
| **Treasurer**    |                      |                      |
| First Name       | {{ $tresfnamePre }}  | {{ $tresfnameUpd }}  |
| Last Name        | {{ $treslnamePre }}  | {{ $treslnameUpd }}  |
| E-mail           | {{ $tresemailPre }}  | {{ $tresemailUpd }}  |
| **Secretary**    |                      |                      |
| First Name       | {{ $secfnamePre }}   | {{ $secfnameUpd }}   |
| Last Name        | {{ $seclnamePre }}   | {{ $seclnameUpd }}   |
| E-mail           | {{ $secemailPre }}   | {{ $secemailUpd }}   |
@endcomponent

@component('mail::subcopy')
**MCL**,
MIMI Database Administrator
@endcomponent

@endcomponent
