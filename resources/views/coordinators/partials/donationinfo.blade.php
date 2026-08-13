    <div class="row">
        <div class="col-auto"><label>M2M Donation:</label></div>
        <div class="col text-end">
        @if ($chPayments->m2m_donation)
            <b>${{ $chPayments->m2m_donation }}</b> on <b>@formatDate($chPayments->m2m_date)</b>
        @else
            No Donation Recorded
        @endif
        </div>
    </div>
    <div class="row">
        <div class="col-auto"><label>Sustaining Chapter Donation:</label></div>
        <div class="col text-end">
        @if ($chPayments->sustaining_donation)
            <b>${{ $chPayments->sustaining_donation }}</b> on <b>@formatDate($chPayments->sustaining_date)</b>
        @else
            No Donation Recorded
        @endif
    </div>
    </div>
