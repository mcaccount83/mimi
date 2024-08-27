@component('mail::message')
<center>
    <img src="images/LOGO-W-MOMS-CLUB-old.jpg" alt="MC" style="width: 125px;">
</center>
<br>
<h1><center>{{ $mailData['conf_name'] }} Conference @if($mailData['reg_name'] != 'None')| {{ $mailData['reg_name'] }} Region @endif</center></h1>
<h4> {{ $mailData['firstName'] }} {{ $mailData['lastName'] }}, Welcome to Our Team!</h4>
<p>Congratulations on your appointment to Big Sister! We are looking forward to working with you and hope you find your new role interesting, fulfilling and especially enjoyable!</p>
<hr>
<h4>Your Mentoring Coordinator and Her Role</h4>
<p>Big Sisters are considered "in training” and most of your training will come on the job. You'll form a close working relationship with your Mentoring Coordinator as she helps guide you through your MOMS Club Volunteer experience.</p>
<p>Your Mentoring Coordinator is your "go to" for anything that you need. While your role is to support the chapters, your Mentoring Coordinator's role is to support YOU.</p>
<p>As a Big Sister, communication (both with your chapters and your Mentoring Coordinator) is vital. You should be checking in with your Mentoring Coordinator every week to ensure you are receiving the information and support you need.</p>
<p>While in training, your Mentoring Coordinator will be more involved in your communication with the chapters. As you start out you'll run everything past her before replying to a chapter. We strive to reply back to the chapter within 24 hours, so if it seems to be taking longer than that due to the nature of the question, you can always let a chapter know that you are still looking into it in order to give them the most accurate and complete answer possible.</p>
<p>The easiest way to accomplish this is to go ahead and try to answer the chapter's question with what you think is correct and send the draft to your Mentoring Coordinator. She may have some additional inputs to add that you hadn't thought about. Once she shares that with you, you'll send the final copy to the chapter. Keep in mind this is part of the training process and something that everyone has done.</p>
<p>After a few months you'll find your writing style and become more comfortable responding to chapters.</p>
<hr>
<br>
<center><b>YOUR MENTORING COORDINATOR</b></center>
    <p><center>{{ $mailData['cor_fname'] }} {{ $mailData['cor_lname'] }}<br>
                <a href="mailto:{{ $mailData['cor_email'] }}">{{ $mailData['cor_email'] }}</a><br>
               {{ $mailData['cor_phone'] }}</center></p>
<br>
<hr>
<b>LET'S GET STARTED!</b>
<p>Complete the Big Sister training in our eLearning Portal to gain a fuller understanding of the role of a Big Sister.</p>
<p><center>https://momsclub.org/elearning/coordinator-training/<br>
    Password: Toolkit2021</center></p>
<hr>
<b>GSUITE</b>
<p>All coordinators will receive and are expected to use an @momsclub.org email address when representing MOMS Club.</p>
<p>A wide variety of documents, spreadsheets and presentations are created, stored and shared using Google Drive and Google Meet is used to interact face-to-face one on one, in small groups or events to hold virtual workshops and other Conference or International level events.</p>
<p>Your assigned email address is {{ $mailData['email'] }}. You should have received an email from Google to activate your account. If you did not receive the email, or if the link has expired, just let me know and we'll send you a new link!
<hr>
<b>MIMI</b>
<p>Although you are probably familiar with MIMI as a board member of your chapter, you will also use MIMI as a coordinator.</p>
<p><center>https://momsclub.org/mimi/login<br>
    Username: {{ $mailData['email'] }}<br>
    Password: TempPass4You</center></p>
<br>
<p>After logging in you will see the coordinator dashboard instead of your chapter's profile page.</p>
<p>It is important to keep your @momsclub.org listed as your email address for your coordinator account because that is the email address your chapters see and will use to contact you. MIMI also does not allow coordinators and board member profiles to use the same email address. So, if you are still on your chapter's board and try to set both accounts up with the same email address you will get an error when trying to log into MIMI.</p>
<hr>
<br>
<center><b>YOUR CHAPTER LIST</b></center>
@if (count($mailData['chapters']) > 0)
    <center>
        <table id="chapterlist" class="table table-bordered table-hover" style="padding-top: 15px">
            <tbody>
                @foreach ($mailData['chapters'] as $chapter)
                    <tr>
                        <td style="padding: 0px; line-height: 1.5;"><center>{{ $chapter->chapter }}, {{ $chapter->state }}</center></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </center>
@else
    <p><center>No chapters have been assigned to you yet.<br>
    You will be notified once your group of chapters has been set.</center></p>
@endif
<br>
<hr>
<b>ADDITIONAL RESOURCES</b>
<p>The Coordinator Toolkit is the all-in-one place where you can find information, documents, resources, links, etc. that you may find helpful to you as a coordinator.<br>
    https://momsclub.org/mimi/admin/toolkit</p>
<p>The list of Chapter Resources is also easily accessible for you to reference when helping chapters with issues they may face.
    https://momsclub.org/mimi/admin/resources</p>
<hr>
<b>YOUR COORDINATOR TEAM</b>
<p>You are never alone! No matter how seasoned you are, you will always have a Coordinator Team around you. Your full coordinator team is cc'd here so you have all of their information and I'm also here for anything that you need!  But, as your Mentoring Coordinator, {{ $mailData['cor_fname'] }} should always be your first point of contact.</p>

<br>
<p><strong>MCL</strong>,<br>
    {{ $mailData['userName'] }}<br>
    {{ $mailData['positionTitle'] }}<br>
    Conference {{ $mailData['conf'] }}, {{ $mailData['conf_name'] }}<br>
    International MOMS Club</p>
@endcomponent
