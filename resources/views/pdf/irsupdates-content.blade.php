 <!-- Second Page without Footer -->
    <div class="subsequent-page">
        <div class="keep-together" style="page-break-inside: avoid;">
            <center>
                <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-mc.png' }}" alt="MC" style="width: 125px;">
            </center>
            <br>
            <p>{{ $pdfData['todayDate'] }}</p>
            <p>Internal Revenue Service<br>
                Ogden, UT  84201</p>
            <p><b>Subordinate Corrections</b><br>
                Taxpayer ID: 77-0125681<br>
                Gen Number: 3706</p>
            @if(count($pdfData['chapterZapList']) > 0)
                <p>Below is a list of subordinates that have disbanded since our last submission on {{ $pdfData['startDate'] }} and <u><b>should be removed from our group immediately.</u></b></p>
                <table>
                    <thead>
                        <tr>
                            <th>EIN#</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pdfData['chapterZapList'] as $chapter)
                            <tr>
                                <td>{{ $chapter->ein ?? '' }}</td>
                                <td>{{ $chapter->name ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if(count($pdfData['chapterAddList']) > 0)
            <p>Below is a list of subordinates organized since our last submission on {{ $pdfData['startDate'] }}.  <u><b>These chapters are good standing and should be added to our list of subordinates.  All chapters have the same fiscal year of July 1st-June 30th.</u></b></p>
            <table>
                <thead>
                    <tr>
                        <th>EIN#</th>
                        <th>Name</th>
                        <th>Pres Name</th>
                        <th>Pres Address</th>
                        <th>Pres City</th>
                        <th>Pres State</th>
                        <th>Pres Zip</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pdfData['chapterAddList'] as $chapter)
                        <tr>
                            <td>{{ $chapter->ein ?? '' }}</td>
                            <td>{{ $chapter->name ?? '' }}</td>
                            <td>{{ ($chapter->pres_first_name ?? '') . ' ' . ($chapter->pres_last_name ?? '') }}</td>
                            <td>{{ $chapter->pres_address ?? '' }}</td>
                            <td>{{ $chapter->pres_city ?? '' }}</td>
                            <td>{{ $chapter->pres_state ?? '' }}</td>
                            <td>{{ $chapter->pres_zip ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            <p>Thank you for your assistance in this matter.  If you have any questions, please contact me by phone or email.</p>
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
    </div>
