
@if ($chDetails->active_status == 1)
    <div class="row">
        <label class="col-auto fw-bold">Primary Coordinator:</label>
        <div class="col text-end">
            <select name="ch_primarycor" id="ch_primarycor" class="form-control" onchange="loadCoordinatorList(this.value)" required>
                <option value="">Select Primary Coordinator</option>
                @foreach($pcList as $coordinator)
                    <option value="{{ $coordinator['cid'] }}"
                        {{ isset($chDetails->primary_coordinator_id) && $chDetails->primary_coordinator_id == $coordinator['cid'] ? 'selected' : '' }}>
                        {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mb-2">
        <span id="display_corlist"></span>
    </div>

@elseif ($chDetails->active_status == 2)
    <div class="row">
        <label class="col-auto fw-bold">Primary Coordinator:</label>
        <div class="col text-end">
            <select name="ch_primarycor" id="ch_primarycor" class="form-control" onchange="loadCoordinatorList(this.value)" required>
                <option value="">Select Primary Coordinator</option>
                    @foreach($pcDetails as $coordinator)
                    <option value="{{ $coordinator['cid'] }}"
                        {{ isset($chDetails->primary_coordinator_id) && $chDetails->primary_coordinator_id == $coordinator['cid'] ? 'selected' : '' }}
                        data-region-id="{{ $coordinator['regid'] }}">
                        {{ $coordinator['cname'] }} {{ $coordinator['cpos'] }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row mb-2">
        <span id="display_corlist"></span>
    </div>
@endif
