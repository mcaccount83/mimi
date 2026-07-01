@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

**Welcome to the {{ $mailData['boardReportRange'] }} Executive Board!**

Welcome, {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }} board members! Congratulations on being elected to the executive board for your chapter. We hope you have a fantastic year in store. Read on for some tips and information that will help you have a successful year. We look forward to working with you!

---

**Officer Checklist**

The following are a few things every Officer needs to know!

- Get all your members involved and protect your officers by having the members vote on everything at the monthly business meeting. Plan something fun at each business meeting so members will want to come — a fun activity or speaker — but also have your members vote on everything that the chapter is doing, being sure to get them to volunteer to help at the same time!
- The IRS requires that all nonprofit groups use their money for true nonprofit purposes, so make sure that your treasury pays less than 15% of your chapter's dues income for party expenses or anything that benefits members specifically (like t-shirts or something that goes directly to the members.) Potluck parties or things that the members pay for directly don't count, and will help keep your dues low, too.
- We want to know what you're doing so we can spread the word about how great you are! Don't be modest — contact your Primary Coordinator once a month and send her and your Secondary Coordinator a copy of your newsletter, calendar or a summary of your activities!
- Let your members know who we are! Please include the name, title and email address of your Primary Coordinator in each month's newsletter/calendar. You should also include our general email address support@momsclub.org.
- Remember you're there specifically for at-home mothers, so keep your activities during the day. One evening activity a month is fine, but the rest need to be during the day so at-home mothers can easily attend.
- Take pride in your name! MOMS Club is a registered service mark, so it's important that everyone uses it correctly. No periods, no apostrophe, and always include your chapter's geographic name so everyone knows you're you!
- Have FUN! Being president is a lot of fun! Delegate, then relax, smile and enjoy your term!

---

**Officer Packet**

You will find the Officer's Resource Packet attached. The officer packet includes more details about a variety of topics including Party/Member Benefit Expenses, Geographic Boundaries, Sistering, Annual Budget, Ideas for Board Meeting Agendas and more.

---

**MOMS Information Management Interface (MIMI)**

MIMI is the database system for International MOMS Club. To access MIMI go to: [https://momsclub.org/mimi/login](https://momsclub.org/mimi/login)

You can log on with the e-mail address we have on file for you. If you are a new board member, your default password is: **TempPass4You**

The chapter president will have access to all chapter information, including all board members. Other board members will have access to chapter information as well as their own details. (All board members have access to MIMI!)

Things to check the first time you log in:

- Change your password if it is still set with the default password.
- Check that all contact details are correct, making updates as necessary.
- Read through your chapter's boundaries. If you feel these are not correct, contact your Primary Coordinator.
- Check out the current website we have listed for your chapter, and update if necessary. Click to have your site linked to the International site, if it is not already.
- Make sure your chapter's e-mail address and the e-mail to give to any inquiries is up to date.
- Note the contact information listed for your chapter's volunteers. If you ever have trouble reaching your Primary Coordinator, you can click on the name of your Secondary Coordinator to email them.
- You can log on to MIMI anytime. If you receive error messages or if you have any questions at all, please let your Primary Coordinator know!

Other Resources available through your MIMI account:

- **Chapter Resources** — Other resources including the Bylaws, Fact Sheets, Sample Files, Digital Logos, etc.
- **BoardList Forum** — The BoardList forum group will give you a chance to interact with other board members on chapter related topics. All board members are automatically added. BoardList is open from August through May.
- **eLearning Portal** — Courses on Board Positions and common topics are available for all board members.

---

**Coordinator Team**

All MOMS Club chapters have an International Coordinator assigned to help them.

{{ $mailData['pcName'] }}
@mailto($mailData['pcEmail'])

She is there for anything that you need! Any questions you have or good news you want to share -- talk to her, she loves to hear from you!

**MCL**,
{{ $mailData['ccName'] }}
{{ $mailData['ccPosition'] }}
{{ $mailData['ccConfName'] }}, {{ $mailData['ccConfDescription'] }}
International MOMS Club
@endcomponent
