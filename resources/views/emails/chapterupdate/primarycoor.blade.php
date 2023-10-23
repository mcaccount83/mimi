@component('mail::message')
# MOMS Club Update

Hello {{ $cor_fnameUpd }}!

The MOMS Club of {{ $chapterNameUpd }}, {{ $chapterStateUpd }} has been updated through the MOMS Information Management Interface.

@component('mail::table')

## President
| Field              | Previous Information | Updated Information |
|--------------------|-----------------------|---------------------|
| First Name         | {{ $chapfnamePre }}  | {{ $chapfnameUpd }} |
| Last Name          | {{ $chaplnamePre }}  | {{ $chaplnameUpd }} |
| E-mail             | {{ $chapteremailPre }} | {{ $chapteremailUpd }} |
| Street             | {{ $streetPre }}     | {{ $streetUpd }} |
| City               | {{ $cityPre }}       | {{ $cityUpd }} |
| State              | {{ $statePre }}      | {{ $stateUpd }} |
| Zip                | {{ $zipPre }}        | {{ $zipUpd }} |
| Country            | {{ $countryPre }}    | {{ $countryUpd }} |
| Phone              | {{ $phonePre }}      | {{ $phoneUpd }} |

## AVP
| Field              | Previous Information | Updated Information |
|--------------------|-----------------------|---------------------|
| First Name         | {{ $avpfnamePre }}   | {{ $avpfnameUpd }} |
| Last Name          | {{ $avplnamePre }}   | {{ $avplnameUpd }} |
| E-mail             | {{ $avpemailPre }}   | {{ $avpemailUpd }} |

## MVP
| Field              | Previous Information | Updated Information |
|--------------------|-----------------------|---------------------|
| First Name         | {{ $mvpfnamePre }}   | {{ $mvpfnameUpd }} |
| Last Name          | {{ $mvplnamePre }}   | {{ $mvplnameUpd }} |
| E-mail             | {{ $mvpemailPre }}   | {{ $mvpemailUpd }} |

## Treasurer
| Field              | Previous Information | Updated Information |
|--------------------|-----------------------|---------------------|
| First Name         | {{ $tresfnamePre }}  | {{ $tresfnameUpd }} |
| Last Name          | {{ $treslnamePre }}  | {{ $treslnameUpd }} |
| E-mail             | {{ $tresemailPre }}  | {{ $tresemailUpd }} |

## Secretary
| Field              | Previous Information | Updated Information |
|--------------------|-----------------------|---------------------|
| First Name         | {{ $secfnamePre }}   | {{ $secfnameUpd }} |
| Last Name          | {{ $seclnamePre }}   | {{ $seclnameUpd }} |
| E-mail             | {{ $secemailPre }}   | {{ $secemailUpd }} |

## Chapter Fields
| Field              | Previous Information | Updated Information |
|--------------------|-----------------------|---------------------|
| EIN                | {{ $einPre }}        | {{ $einUpd }} |
| EIN Letter         | {{ $einLetterPre }}  | {{ $einLetterUpd }} |
| Name               | {{ $chapterNamePre }} | {{ $chapterNameUpd }} |
| State              | {{ $statePre }}      | {{ $stateUpd }} |
| Inquiries Contact  | {{ $inConPre }}      | {{ $inConUpd }} |
| Inquiries Notes    | {{ $inNotePre }}     | {{ $inNoteUpd }} |
| Chapter E-mail     | {{ $chapemailPre }}  | {{ $chapemailUpd }} |
| PO Box             | {{ $poBoxPre }}      | {{ $poBoxUpd }} |
| Website URL        | {{ $webUrlPre }}     | {{ $webUrlUpd }} |
| Website Link Status|
@if ($weblinkStatusPre == 1)
- Linked
@elseif ($weblinkStatusPre == 2)
- Link Requested
@else
- Do Not Link
@endif
|
@if ($weblinkStatusUpd == 1)
- Linked
@elseif ($weblinkStatusUpd == 2)
- Link Requested
@else
- Do Not Link
@endif |
| E-Group            | {{ $egroupPre }}     | {{ $egroupUpd }} |
| Chapter Boundaries | {{ $boundPre }}      | {{ $boundUpd }} |
| Primary Coordinator| {{ $cor_fnamePre }} {{ $cor_lnamePre }} | {{ $cor_fnameUpd }} {{ $cor_lnameUpd }} |
| Additional Information | {{ $addInfoPre }} | {{ $addInfoUpd }} |
| Chapter Status     |
@if ($chapstatusPre == 1)
- Operating Ok
@elseif ($chapstatusPre == 4)
- On Hold Do Not Refer
@elseif ($chapstatusPre == 5)
- Probation
@elseif ($chapstatusPre == 6)
- Probation Do Not Refer
@endif
|
@if ($chapstatusUpd == 1)
- Operating Ok
@elseif ($chapstatusUpd == 4)
- On Hold Do Not Refer
@elseif ($chapstatusUpd == 5)
- Probation
@elseif ($chapstatusUpd == 6)
- Probation Do Not Refer
@endif |
| Status Notes      | {{ $chapNotePre }}   | {{ $chapNoteUpd }}   |
@endcomponent

@component('mail::subcopy')
**MCL**,
MIMI
@endcomponent

@endcomponent
