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
        Grant Request for {{ $pdfData['first_name'] }} {{ $pdfData['last_name'] }}</h2></center>

    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>GRANT INFORMATION</b>
    <hr>
    </div>
    <p><strong>BEFORE YOU BEGIN</strong><br>
        Please read this section before filling out the questions!</p>
    <p>If your chapter is requesting assistance from the Mother-to-Mother Fund for one of your members, please read the Mother-to-Mother Fund Fact Sheet. It contains important information on what kinds of grants can be given and what kinds cannot.</p>
    <p>Before you ask for a grant, be sure the situation fits what we can help. There are many situations we cannot help with – divorce, unemployment, and birth defects are a few. We understand those are very difficult challenges for any mother, but they cannot be helped by the Fund.</p>
    <p>If the situation might qualify for a grant, first ask the mother-in-need if she wants you to apply for her. Some people are very private. They do not want assistance nor for people to know they have a problem. If that is the case, we cannot give a grant. While we do not publish the names of grant recipients, we do publish information about the grants that are given, and it would be easy for people who know the mother to figure out if a grant was given and how much.</p>
    <p>Only a chapter may apply for a grant for a member. The grant request should be filled out by a member of the Executive Board. That officer will be the liaison between the Mother-to-Mother Fund Committee and the mother-in-need. A mother-in-need may not apply for a grant on her own. The request has to come from the chapter, but the chapter may work with the mother to answer the questions here. If an officer is not available, due to a natural disaster or other problem, then another member may submit the request, but the Board will be contacted to confirm the information.</p>
    <p>Be as specific as possible in answering the questions. Be sure to fill out all questions before submitting the form!</p>
    <table>
        <tbody>
           <tr><td><strong>I have read this section and understand the limits of the fund:</strong><br>
                {{ $pdfData ['understood'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>The mother has been asked if she wants you to submit this grant on her behalf.</strong><br>
                {{ $pdfData ['member_agree'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>The mother has agreed to accept a grant request if one is given.</strong><br>
                {{ $pdfData ['member_accept'] == 1 ? 'YES' : 'NO' }}</td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>BOARD MEMBER SUBMTITING REQUEST</b>
    <hr>
    </div>
    <table>
        <tbody>
           <tr><td><strong>Name</strong><br>
                {{ $pdfData ['board_name'] }}</td></tr>
           <tr><td><strong>Position</strong><br>
                {{ $pdfData ['board_position'] }}</td></tr>
           <tr><td><strong>Phone</strong><br>
                {{ $pdfData ['board_phone'] }}</td></tr>
            <tr><td><strong>Email</strong><br>
                {{ $pdfData ['board_email'] }}</td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
    <b>MEMBER IN NEED</b>
    <hr>
    </div>
    <table>
        <tbody>
           <tr><td><strong>Name</strong><br>
                {{ $pdfData ['first_name'] }} {{ $pdfData ['last_name'] }}</td></tr>
           <tr><td><strong>Email</strong><br>
                {{ $pdfData ['email'] }}</td></tr>
           <tr><td><strong>Phone</strong><br>
                {{ $pdfData ['phone'] }}</td></tr>
           <tr><td><strong>Can the member be reached at the number above?</strong><br>
                {{ $pdfData ['reachable'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>Additional number</strong><br>
                {{ $pdfData ['alt_phone'] }}</td></tr>
           <tr><td><strong>Address</strong><br>
                {{ $pdfData ['address'] }}<br>
                {{ $pdfData ['city'] }}, {{ $pdfData ['state'] }} {{ $pdfData ['zip'] }}<br>
                {{ $pdfData ['country'] }}
            </td></tr>
           <tr><td><strong>How long has the mother been a member?</strong><br>
                {{ $pdfData ['member_length'] }}</td></tr>
           <tr><td><strong>Who is living in the home?</strong><br>
                {{ $pdfData ['household_members'] }}</td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
     <b>EXPLANATION OF SITUATION</b>
    <hr>
    </div>
    <table>
        <tbody>
           <tr><td><strong>Please provide a summary of the situation</strong><br>
                {{ $pdfData ['situation_summary'] }}</td></tr>
           <tr><td><strong>What has the family done to improve or handle the situation?</strong><br>
                {{ $pdfData ['family_actions'] }}</td></tr>
           <tr><td><strong>What is the financial situation of the family?</strong><br>
                {{ $pdfData ['financial_situation'] }}</td></tr>
           <tr><td><strong>What are the family's most pressing needs right now?</strong><br>
                {{ $pdfData ['pressing_needs'] }}</td></tr>
           <tr><td><strong>Is there anything else the family needs?</strong><br>
                {{ $pdfData ['other_needs'] }}</td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
     <b>GRANT REQUEST DETAILS</b>
    <hr>
    </div>
    <table>
        <tbody>
           <tr><td><strong>What amount is being requested? What will it be used for?</strong><br>
                {{ $pdfData ['amount_requested'] }}</td></tr>
           <tr><td><strong>How has the chapter supported the member up to this point?</strong><br>
                {{ $pdfData ['chapter_support'] }}</td></tr>
           <tr><td><strong>Is there anything else we should know?</strong><br>
                {{ $pdfData ['additional_info'] }}</td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
     <b>CHAPTER BACKING & AFFIRMATION</b>
    <hr>
    </div>
    <table>
        <tbody>
           <tr><td><strong>Has the chapter ever asked for a grant for this mother or family in the past?</strong><br>
                {{ $pdfData ['previous_grant'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>Does the chapter stand behind this request?</strong><br>
                {{ $pdfData ['chapter_backing'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>Has the chapter donated to the Mother-to-Mother Fund?</strong><br>
                {{ $pdfData ['m2m_donation'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>I affirm that the information in this submission is true:</strong><br>
                {{ $pdfData ['affirmation'] == 1 ? 'YES' : 'NO' }}</td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
</div>
</body>
</html>
