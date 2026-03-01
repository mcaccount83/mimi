    <div class="row">
        <div class="col-auto fw-bold">M2M Donation:</div>
        <div class="col text-end">
        @if ($chPayments->m2m_donation)
            <b>${{ $chPayments->m2m_donation }}</b> on <b><span class="date-mask">{{ $chPayments->m2m_date }}</span></b>
        @else
            No Donation Recorded
        @endif
        </div>
    </div>
    <div class="row">
        <div class="col-auto fw-bold">Sustaining Chapter Donation:</div>
        <div class="col text-end">
        @if ($chPayments->sustaining_donation)
            <b>${{ $chPayments->sustaining_donation }}</b> on <b><span class="date-mask">{{ $chPayments->sustaining_date }}</span></b>
        @else
            No Donation Recorded
        @endif
    </div>
    </div>
