@component('mail::message')
# New Coordinator Application

Your Application has been Successfully Submitted!<br>
<br>
My name is {{ $mailData['ccName'] }} and I am the {{ $mailData['ccPosition'] }} for your area.<br>
<br>
I am excited that you have decided to become a MOMS Club Coordinator!  I will be following up with a more personal email to discuss our specific needs and where you best
fit into our team. However, if you have any questions in the meantime, please do not hesitate to reach out and ask!<br>
<br>
<strong>MCL</strong>,<br>
{{ $mailData['ccName'] }}<br>
{{ $mailData['ccPosition'] }}<br>
{{ $mailData['ccConfName'] }}, {{ $mailData['ccConfDescription'] }}<br>
International MOMS Club
@endcomponent
