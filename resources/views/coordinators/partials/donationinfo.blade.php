    <div class="row">
        <div class="col-auto fw-bold">M2M Donation:</div>
        <div class="col text-end">
        @if ($chPayments->m2m_donation)
            <b>${{ $chPayments->m2m_donation }}</b> on <b>@formatDate($chPayments->m2m_date)</b>
        @else
            No Donation Recorded
        @endif
        </div>
    </div>
    <div class="row">
        <div class="col-auto fw-bold">Sustaining Chapter Donation:</div>
        <div class="col text-end">
        @if ($chPayments->sustaining_donation)
            <b>${{ $chPayments->sustaining_donation }}</b> on <b>@formatDate($chPayments->sustaining_date)</b>
        @else
            No Donation Recorded
        @endif
    </div>
    </div>
