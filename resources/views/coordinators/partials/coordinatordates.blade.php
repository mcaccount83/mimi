<div class="row">
        <div class="col-auto fw-bold">Start Date:</div>
        <div class="col text-end">
            @formatDate($cdDetails->coordinator_start_date)
            </div>
        </div>
        <div class="row">
        <div class="col-auto fw-bold">Last Promotion Date:</div>
        <div class="col text-end">
            @formatDate($cdDetails->last_promoted)
        </div>
        </div>
        <div class="row">
        <div class="col-auto fw-bold">Home Chapter:</div>
        <div class="col text-end">
            {{ $cdDetails->home_chapter }}
                </div>
        </div>
