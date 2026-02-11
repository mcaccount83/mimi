<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grant List</title>
</head>
<body>

    <table width="100%" style="border: none; margin-bottom: 20px;">
    <tr>
        <td width="20%" style="vertical-align: top; padding: 10px;">
            <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc-noarch.png' }}" alt="MC" style="width: 120px;">
        </td>
        <td width="80%" style="text-align: center; vertical-align: middle;">
            <h2 style="margin: 0;">International MOMS Club<br>
                Mother-to-Mother Fund Grants<br>
                January 1994 - Present<br>
                ${{ number_format($totalLifetimeGrants, 2) }}</h2>
        </td>
    </tr>
</table>
    {{-- <center>
        <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc-noarch.png' }}" alt="MC" style="width: 150px;">
    </center>
<br>

                    <div class="col-md-12" style="text-align: center;">
    <h2>Mother-to-Mother Fund Grants<br>
        January 1994 - Present<br>
        ${{ number_format($totalLifetimeGrants, 2) }}</h2>
</div> --}}

<p>Below are the situations for which grants have been given since the beginning of the International MOMS Club’s Mother-To-Mother Fund in 1994, through the date of this publication.</p>
<p>As you’ll see, each situation is unique and extremely devastating to the member and/or her family. Each grant request was considered individually and each situation was weighed for severity and the ability of a family to prepare for the situation.</p>
<p>The Fund was originally created to help when natural disasters struck. In 1996, we were able to expand the Fund to also help with devastating personal financial disasters.</p>
<p>We believe you’ll see from the situations below, that the Fund has fulfilled its goal of helping MOMS Club members suffering from unexpected and devastating natural and personal disasters.</p>
<p>The amount of money we have been able to give in grants has grown over the years, both in the number of grants and the possible size of each. We hope that the Fund will continue to grow so we can make an even more significant positive impact in the lives of our members-in-need in the future.</p>

<br>

         <table width="100%" style="border-collapse: collapse;">
    <thead>
        <tr style="border-bottom: 2px solid #333; font-weight: bold;">
            <td width="15%">Date</td>
            <td width="10%">State</td>
            <td width="55%">Description</td>
            <td width="20%" style="text-align: right; padding: 5px;">Amount</td>
        </tr>
    </thead>
    <tbody>
        @foreach($grantsByFiscalYear as $fiscalYear => $grants)
            @if($grants->count() > 0)
                @foreach($grants as $list)
                    <tr style="border-bottom: 1px solid #555;">
                        <td>{{ \Carbon\Carbon::parse($list->submitted_at)->format('M Y') }}</td>
                        <td>{{$list->chapterstate->state_short_name}}</td>
                        <td>{{ $list->review_description}}</td>
                        <td style="text-align: right; padding: 5px;">${{ number_format($list->amount_awarded, 2) }}</td>
                    </tr>
                @endforeach
                <tr style="border-top: 2px solid #333; border-bottom: 2px solid #333;font-weight: bold;">
                    <td colspan="3" style="text-align: right; padding: 5px;">Total for Fiscal Year {{ $fiscalYear }}:</td>
                    <td style="text-align: right; padding: 5px;">${{ number_format($grants->sum('amount_awarded'), 2) }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="4" style="padding: 5px; font-style: italic;">No grants for fiscal year {{ $fiscalYear }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
                <br>
 <div class="col-md-12" style="text-align: center;">
    <h2>Your donations to the Mother-To-Mother Fund<br>
        made these grants possible!</h2>
                        <br>

                 <center>
        <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc-noarch.png' }}" alt="MC" style="width: 200px;">
    </center>

    <br>



</body>
</html>
