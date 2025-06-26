
    <center>
        <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc.png' }}" alt="MC" style="width: 125px;">
    </center>
<br>
<div class="keep-together" style="page-break-inside: avoid;">
    <br>
    <p>{{ $pdfData['todayDate'] }}</p>
    <br>
    <p>Internal Revenue Service<br>
        Ogden, UT  84201</p>
    <p><b>Subordinate Update/Name Change</b><br>
        Taxpayer ID: 77-0125681<br>
        Gen Number: 3706</p>
    <p>The below subordinate has changed their name and contact. Their IRS records need to be updated so they can request a new EIN verification letter. Below is their updated information:</p>
    <p><b>{{ $pdfData['chapterEIN'] }}</b><br>
        MOMS Club of {{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }}<br>
        (formerly known as MOMS Club of {{ $pdfData['chNamePrev'] }}, {{ $pdfData['chapterState'] }})<br>
        c/o {{$pdfData['presName']}}<br>
        {{$pdfData['presAddress']}}<br>
        {{$pdfData['presCity']}}, {{$pdfData['presState']}} {{$pdfData['presZip']}}</p>
    <p>Thank you for your assistance in this matter.  If you have any questions, please contact me by phone or email.  Unless I receive information from your office
        to the contrary, I will instruct our subordinate to contact the IRS for a new EIN verification letter after {{ $pdfData['twoMonthsDate'] }}.</p>
    <br>
    <p>Sincerely,</p>
    <br>
    <br>
    <p>{{ $pdfData['einName'] }}<br>
        EIN/990N Compliance<br>
        {{ $pdfData['einEmail'] }}<br>
        {{ $pdfData['einPhone'] }}<br>
        International MOMS Club<sub>&reg;</sub></p>
</div>
