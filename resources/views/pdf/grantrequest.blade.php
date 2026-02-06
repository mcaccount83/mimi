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
           <tr><td><strong>I have read this section and understand the limits of the fund as well as the requirements stated.</strong><br>
                {{ $pdfData ['understood'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>Some people do not want a grant request to be submitted for them. The mother has been asked if she wants you to submit this grant on her behalf.</strong><br>
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
           <tr><td><strong>How long has the mother-in-need been a member of your chapter? You may answer with a join date or the number of years/months she has
            been in your chapter. Is she a member now or has she "retired" or moved from your chapter?</strong><br>
                {{ $pdfData ['member_length'] }}</td></tr>
           <tr><td><strong>Who is living in the home? Is there a spouse? How many family members and what are the ages of the children?</strong><br>
                {{ $pdfData ['household_members'] }}</td></tr>
            <tr><td><strong>If the member's home is uninhabitable, where is she living now? Please provide mailing address if different from above.</strong><br>
                {{ $pdfData ['alt_address'] }}</td></tr>
            <tr><td><strong>Has the chapter ever asked for a grant for this mother or family in the past?</strong><br>
                {{ $pdfData ['previous_grant'] == 1 ? 'YES' : 'NO' }}</td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
    <hr>
     <b>EXPLANATION OF SITUATION</b>
    <hr>
    </div>
    <p><strong>Please be as specific as possible.</strong></p>
        <p>Did their house catch on fire? If so, how did the fire start and what was lost? If someone has a life-threatening illness, be specific.
            Don’t just say they are sick, but tell us what their illness is and how it is impacting the family and their finances.</p>
        <p>if the need is for childcare while the mother is undergoing treatment, tell us how much that childcare will cost, how many weeks it will be needed,
                    why they cannot afford it and why your chapter cannot help with that. If they cannot afford their medication, what medication do they need and how much would it cost them?
                    If they need help traveling to treatment, where is the treatment, how many times will they need to go and how much will each trip cost?</p>
        <p>The more specific information we have, the faster the Committee can make its decision.</p>
    <table>
        <tbody>
           <tr><td><strong>Please provide a summary of the situation. What happened, how did it happen and what is the result of it?</strong><br>
                {{ $pdfData ['situation_summary'] }}</td></tr>
           <tr><td><strong>What has the family done to improve or handle the situation?</strong><br>
                {{ $pdfData ['family_actions'] }}</td></tr>
           <tr><td><strong>What is the financial situation of the family? Do they have insurance that will help with this? How much will it cover? Do they have savings?
            If so, how much? Are they getting help from their family or any other grants or loans?</strong><br>
                {{ $pdfData ['financial_situation'] }}</td></tr>
           <tr><td><strong>What are the family’s most pressing needs right now? What are they having to do without because of this situation?</strong><br>
                {{ $pdfData ['pressing_needs'] }}</td></tr>
           <tr><td><strong>Is there anything else that the family needs and is having to do without because of the situation?</strong><br>
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
           <tr><td><strong>A chapter should always be the first ones to help a member-in-need. How has the chapter supported the member up to this point? Has the chapter done any
            fundraisers or made any donations to the family? What are the chapter’s future plans to help this family?</strong><br>
                {{ $pdfData ['chapter_support'] }}</td></tr>
           <tr><td><strong>Is there anything else we should know about this family or their situation?</strong><br>
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

           <tr><td><strong>Does the chapter stand behind this request for a grant? Has the Executive Board discussed the situation and decided to submit this request? And does the
            Executive Board assure the Mother-to-Mother Fund Committee that the information in this request is true?</strong><br>
                {{ $pdfData ['chapter_backing'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>Has the chapter donated to the Mother-to-Mother Fund in the past?</strong><br>
                {{ $pdfData ['m2m_donation'] == 1 ? 'YES' : 'NO' }}</td></tr>
           <tr><td><strong>I affirm that the information in this submission is true and the mother-in-need agrees with the submission and the information herein.</strong><br>
                {{ $pdfData ['affirmation'] == 1 ? 'YES' : 'NO' }}</td></tr>
        </tbody>
    </table>
    <br>
    <div class="keep-together" style="page-break-inside: avoid;">
</div>
</body>
</html>
