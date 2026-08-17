<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .keep-together {
        page-break-inside: avoid;
    }
    </style>
    <title>{{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }} | {{ $financialReportName }}</title>
</head>
<body>
    <center><h2>MOMS Club of {{ $pdfData['chapterName'] }}, {{ $pdfData['chapterState'] }}<br>
        @if ($pdfData['final_report_received'] == 1)
            Board Election Report
        @else
            {{ $boardReportName }}
        @endif
        </h2></center>

    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>BOUNDARIES</b>
    <hr>
    </div>
    <table width="100%">
        <tbody>
             <tr><td>Boundaries:</td>
                    <td>{{ $pdfData['chapterBoundaries'] }}</td></tr>
           <tr><td>Are your listed boundaries correct?</td>
           <td><strong>{{ $pdfData ['boundary_issues'] == 1 ? 'YES' : 'NO' }} </strong></td></tr>     
            @if ($pdfData['boundary_issues'] != 1)
            <tr><td>Please indicate which part of the Boundaries not NOT match our records:</td>
                <td>{{ $pdfData['boundary_issue_notes'] }}</td></tr>
            @endif
            </tbody>
        </table>
    <br>

    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>OUTGOINGBOARD MEMBERS</b>
    <hr>
    </div>
    <table width="100%">
        <tbody>
            <tr><td>President:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr><td>Administrative Vice President:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr><td>Membership Vice President:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr><td>Treasurer:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr><td>Secretary:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>INCOMING BOARD MEMBERS</b>
    <hr>
    </div>
    <table width="100%">
        <tbody>
            <tr><td>President:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr><td>Administrative Vice President:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr><td>Membership Vice President:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr><td>Treasurer:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr><td>Secretary:</td></tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@mailto($PresDetails->email)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>@tel($PresDetails->phone)</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->street_address}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}</td><tr>
            <tr><td>&nbsp;&nbsp;&nbsp;</td><td>{{$PresDetails->country->short_name}}</td><tr>
        </tbody>
    </table>
    <br>

    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>CHAPTER INFORMATION</b>
    <hr>
    </div>
   <table width="100%">
        <tbody>
             <tr><td>Boundaries:</td>
                    <td>{{ $pdfData['chapterBoundaries'] }}</td></tr>
           <tr><td>Are your listed boundaries correct?</td>
           <td><strong>{{ $pdfData ['boundary_issues'] == 1 ? 'YES' : 'NO' }} </strong></td></tr>     
            @if ($pdfData['boundary_issues'] != 1)
            <tr><td>Please indicate which part of the Boundaries not NOT match our records:</td>
                <td>{{ $pdfData['boundary_issue_notes'] }}</td></tr>
            @endif
            </tbody>
        </table>
    <br>
    
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>SUBMISSION INFORMATION</b>
    <hr>
    Submitted by: {{ $pdfData ['completedName']}}<br>
    Email: {{ $pdfData ['completedEmail']}}<br>
    Date: {{ $pdfData ['submitted']}}
</div>
</body>
</html>
