@component('mail::message')
# MOMS Club of {{ $mailData['chapterName'] }}, {{ $mailData['chapterState'] }}

**{{ $mailData['presFirstName'] }},**

CONGRATULATIONS on getting your chapter officially started! I've really enjoyed getting to know you and going through the startup process
with you. I'm excited to see what your new chapter will be able to accomplish!

---

**Your Primary Coordinator and Her Role**

To help you through your MOMS Club journey, you will be assigned a Primary Coordinator. I've cc'd her on this email so you have her contact
information. Be expecting to hear from her with a formal introduction soon.

In the meantime, below are some "Next Steps" to consider as you get started with your new chapter.

---

**Step 1 - Meet your Coordinator**

All MOMS Club chapters have an International Coordinator assigned to help them. She is there for anything that you need! Any questions
you have or good news you want to share -- talk to her, she loves to hear from you!

{{ $mailData['pcName'] }}
@mailto($mailData['pcEmail'])

---

**Step 2 - MOMS Information Management Interface (MIMI)**

MIMI is where important information about your chapter is held. When logged in you can see your EIN number, boundaries, update your
contact information, add additional board members, pay your annual re-registration dues, link your website, see who your Coordinators
are, find additional resources and more. Always keep your information up to date in MIMI as that is our official record of your chapter.

[https://momsclub.org/mimi/login](https://momsclub.org/mimi/login)
Username: {{ $mailData['presEmail'] }}
Password: TempPass4You

---

**Step 3 - Open a Bank Account**

Now that you have your EIN Number ({{ $mailData['chapterEIN'] }}) you should open a Chapter Checking Account. Check your area
for banks that offer free accounts to non-profits. In addition to your EIN, you will likely need a copy of the Group Exemption Letter (attached).

---

**Step 4 - Set your Dues**

Most chapters charge between $20-$35 per year to their members. You will have an annual re-registration payment due to International
at $5/member ($50 minimum) so be sure to include that in your calculations. Read more about how income from dues may be used.

---

**Step 5 - Hold your first Meeting**

As a chapter you'll hold a monthly meeting where business can be discussed and voted on. All members (and potential members) are
invited to attend the monthly meeting. It may be held at a local park, a library, a church, whatever works for your area, time of year and budget.

---

**Step 6 - Look for Board Members**

To be successful, you will need help! As you start getting new members, be on the lookout for any potential board members. If
someone isn't interested in a board position, get them involved as a playgroup or service project coordinator. The more people
are personally involved, the more interest they will have in helping the new chapter to become successful!

---

**Step 7 - Additional Resources**

As previously mentioned, be sure to check the "Chapter Resources" section in your MIMI profile. You'll find Fact Sheets on various
topics, sample files, logo downloads, etc.

**MCL**,
{{ $mailData['userName'] }}
{{ $mailData['userPosition'] }}
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}
International MOMS Club
@endcomponent
