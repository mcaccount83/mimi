 <!-- First Page with Footer -->
    <div class="first-page">
        <!-- Fax Cover Page Content -->
        <div class="keep-together" style="page-break-inside: avoid;">
            <center><h1>FAX</h1></center>
            <table class="cover-table">
                <tr>
                    <td class="label">DATE:</td>
                    <td>{{ $pdfData['todayDate'] }}</td>
                </tr>
                <tr>
                    <td class="label">PAGES:</td>
                    <td>{{ $pdfData['totalPages'] }} Pages (including cover)</td>
                </tr>
                <tr>
                    <td class="label">TO:</td>
                    <td>Internal Revenue Service, EO Entity</td>
                </tr>
                <tr>
                    <td class="label">FAX:</td>
                    <td>855-214-7520</td>
                </tr>
                <tr>
                <td class="label">FROM:</td>
                    <td>International MOMS Club<br>
                        {{ $pdfData['einName'] }}, EIN/990N Compliance</td>
                </tr>
                <tr>
                    <td class="label">EMAIL:</td>
                    <td>{{ $pdfData['einEmail'] }}</td>
                </tr>
                <tr>
                    <td class="label">PHONE:</td>
                    <td>{{ $pdfData['einPhone'] }}</td>
                </tr>
                <tr>
                    <td class="label">FAX:</td>
                    <td>254-237-2791</td>
                </tr>
                <tr>
                    <td class="label">REGARDING:</td>
                    <td>International MOMS Club<br>
                        Taxpayer ID Number:  77-0125681<br>
                        GEN Number:  3706</td>
                </tr>
            </table>
             <table class="cover-table">
                <tr>
                    <td>
                        <br>
                        Subordinate corrections. Includes any additions and deletions from {{ $pdfData['startFormatted'] }} - {{ $pdfData['todayFormatted'] }}.<br>
                        <br>
                        Thank you.<br>
                        <br>
                        {{ $pdfData['followPages'] }} page(s) to follow.<br>
                        <br>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Fixed footer - only on first page -->
        <div class="footer">
            The information contained in this transmission may contain confidential information, including patient information protected under federal and state law. This document is intended for the Internal Revenue Service only. If you are not the intended recipient, you are hereby notified that any disclosure or distribution of this information is in violation of HIPAA confidentiality and prohibited. If you are not the intended recipient, please contact the sender by reply email and delete all copies. This fax disclaimer is present on the cover sheet and serves as a warning to ensure privacy.
        </div>
    </div>
