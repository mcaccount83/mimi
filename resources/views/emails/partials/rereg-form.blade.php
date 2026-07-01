To calculate your payment:

- Determine how many people paid dues to your chapter from **{{ $mailData['startRange'] }}** through **{{ $mailData['endRange'] }}**
- Add in any people who paid reduced dues or had their dues waived due to financial hardship
- If this total amount of members is less than 10, make your check for the amount of $50
- If this total amount of members is 10 or more, multiply the number by $5.00 to get your total amount due
- Payments received after the last day of **{{ $mailData['startMonth'] }}** should include a late fee of $10

Pay online through your MIMI account: [https://momsclub.org/mimi](https://momsclub.org/mimi)

OR to pay by mail — be sure your full chapter name, including state abbreviation, is on your check so that the re-registration can be credited properly.

- Make check payable to "MOMS Club"
- Please write "Chapter Re-Registration" in the Memo field of your check
- As requested with all chapter checks, be sure two Executive Board members sign the check
- Print this page, fill out the box below and mail the page, along with the re-registration, check to:

MOMS Club
Chapter Re-Registration
208 Hewitt Dr., Ste 103 #328
Waco, TX 76712

<table style="width:100%; border-collapse: collapse; border: 1px solid #000; font-family: inherit; font-size: inherit;">
    <tbody>
        <tr>
            <td style="padding: 8px;">
                <strong>MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}</strong><br>
                Anniversary Month: <strong>{{ $mailData['startMonth'] }}</strong>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px;">
                <u>$</u>________ Re-Registration Dues enclosed for _______ members<br>
                <u>$</u>________ Late fee included<br>
                <u>$</u>________ Sustaining Chapter<br>
                <u>$</u>________ Total<br>
                <br>
                <small>Sustaining chapter donations are <u>voluntary</u> and in addition to your chapter's
                re-registration dues. At this time, the minimum sustaining chapter donation is $100. The
                donation benefits the International MOMS Club, which is a 501(c)(3) public charity. Your
                support to the MOMS Club is a service project for your chapter and should be included in
                its own line on your chapter's Annual and Financial Reports. Your donation will help us
                keep dues low and help new and existing chapters in the U.S. and around the world.</small>
            </td>
        </tr>
    </tbody>
</table>
