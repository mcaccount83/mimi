@component('mail::message')
# {{ $mailData['cdConf'] }} Conference @if($mailData['cdRegion'] != 'None') | {{ $mailData['cdRegion'] }} Region @endif

## {{ $mailData['cdName'] }}, Welcome to Our Team!

Congratulations on your appointment to Big Sister! We are looking forward to working with you and hope you
find your new role interesting, fulfilling and especially enjoyable!

---

**Your Mentoring Coordinator and Her Role**

Big Sisters are considered "in training" and most of your training will come on the job. You'll form a close
working relationship with your Mentoring Coordinator as she helps guide you through your MOMS Club Volunteer
experience.

Your Mentoring Coordinator is your "go to" for anything that you need. While your role is to support the
chapters, your Mentoring Coordinator's role is to support YOU.

As a Big Sister, communication (both with your chapters and your Mentoring Coordinator) is vital. You should
be checking in with your Mentoring Coordinator every week to ensure you are receiving the information and
support you need.

While in training, your Mentoring Coordinator will be more involved in your communication with the chapters.
As you start out you'll run everything past her before replying to a chapter. We strive to reply back to the
chapter within 24 hours, so if it seems to be taking longer than that due to the nature of the question, you
can always let a chapter know that you are still looking into it in order to give them the most accurate and
complete answer possible.

The easiest way to accomplish this is to go ahead and try to answer the chapter's question with what you think
is correct and send the draft to your Mentoring Coordinator. She may have some additional inputs to add that
you hadn't thought about. Once she shares that with you, you'll send the final copy to the chapter. Keep in
mind this is part of the training process and something that everyone has done.

After a few months you'll find your writing style and become more comfortable responding to chapters.

---

**Your Mentoring Coordinator**

{{ $mailData['cdReportTo'] }}
@mailto($mailData['cdReportEmail'])
{{ $mailData['cdReportPhone'] }}

---

**Let's Get Started!**

Complete the Big Sister training in our eLearning Portal to gain a fuller understanding of the role of a Big Sister.

[https://momsclub.org/elearning/coordinator-training/](https://momsclub.org/elearning/coordinator-training/)
Password: Toolkit2021

---

**GSuite**

All coordinators will receive and are expected to use an @momsclub.org email address when representing MOMS Club.

A wide variety of documents, spreadsheets and presentations are created, stored and shared using Google Drive
and Google Meet is used to interact face-to-face one on one, in small groups or events to hold virtual
workshops and other Conference or International level events.

Your assigned email address is {{ $mailData['cdEmail'] }}. You should have received an email from Google to
activate your account. If you did not receive the email, or if the link has expired, just let me know and
we'll send you a new link!

---

**MIMI**

Although you are probably familiar with MIMI as a board member of your chapter, you will also use MIMI as a coordinator.

[https://momsclub.org/mimi/login](https://momsclub.org/mimi/login)
Username: {{ $mailData['cdEmail'] }}
Password: TempPass4You

After logging in you will see the coordinator dashboard instead of your chapter's profile page.

It is important to keep your @momsclub.org listed as your email address for your coordinator account because
that is the email address your chapters see and will use to contact you. MIMI also does not allow coordinators
and board member profiles to use the same email address. So, if you are still on your chapter's board and try
to set both accounts up with the same email address you will get an error when trying to log into MIMI.

---

**Your Chapter List**

@if (count($mailData['cdChapters']) > 0)
@foreach ($mailData['cdChapters'] as $chapter)
- {{ $chapter->name }}, {{ $chapter->state->state_short_name }}
@endforeach
@else
No chapters have been assigned to you yet. You will be notified once your group of chapters has been set.
@endif

---

**Additional Resources**

The Coordinator Toolkit is the all-in-one place where you can find information, documents, resources, links,
etc. that you may find helpful to you as a coordinator.
[https://momsclub.org/mimi/resources/toolkit](https://momsclub.org/mimi/resources/toolkit)

The list of Chapter Resources is also easily accessible for you to reference when helping chapters with issues
they may face.
[https://momsclub.org/mimi/resources/resources](https://momsclub.org/mimi/resources/resources)

---

**Your Coordinator Team**

You are never alone! No matter how seasoned you are, you will always have a Coordinator Team around you. Your
full coordinator team is cc'd here and listed in your MIMI profile so you have all of their information and
I'm also here for anything that you need! But, as your Mentoring Coordinator, {{ $mailData['cdReportFName'] }}
should always be your first point of contact.

**MCL**,
{{ $mailData['userName'] }}
{{ $mailData['userPosition'] }}
{{ $mailData['userConfName'] }}, {{ $mailData['userConfDesc'] }}
International MOMS Club
@endcomponent
