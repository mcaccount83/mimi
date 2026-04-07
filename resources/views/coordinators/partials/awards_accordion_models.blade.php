
            <!-- Modal for editing award -->
            @foreach($reportYears as $year)
    @foreach($year->awardBadges as $badge)
    <div class="modal fade" id="editBadgeModal{{ $badge->id }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $badge->eoyAward->award_type }}</h3>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Fiscal Year</label>
                        <select class="form-control" id="reportYear{{ $badge->id }}" disabled>
                            @foreach($reportYears as $year)
                                <option value="{{ $year->id }}" {{ $badge->report_year_id == $year->id ? 'selected' : '' }}>
                                    {{ $year->fiscal_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Award Type</label>
                        <select class="form-control" id="eoyAward{{ $badge->id }}" disabled>
                            @foreach($eoyAwards as $award)
                                <option value="{{ $award->id }}" {{ $badge->eoy_award_id == $award->id ? 'selected' : '' }}>
                                    {{ $award->award_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Current Badge</label><br>
                        @if($badge->file_path)
                            <img src="https://drive.google.com/thumbnail?id={{ $badge->file_path }}&sz=w80" style="max-height: 80px;" class="mb-2">
                        @else
                            <span class="text-muted">No badge uploaded yet.</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label>Replace Badge Image (PNG)</label>
                        <input type="file" class="form-control" id="fileName{{ $badge->id }}" accept=".png">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger bg-gradient mb-2" data-bs-dismiss="modal"><i class="bi bi-x-lg me-2"></i>Close</button>
                    <button type="button" class="btn btn-success bg-gradient mb-2" onclick="updateAwardBadge({{ $badge->id }})"><i class="bi bi-floppy-fill me-2"></i>Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endforeach

            <!-- Modal for adding award -->
             <div class="modal fade" id="modal-add-badge">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Award Badge</h4>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Fiscal Year</label>
                    <select name="reportYearNew" class="form-control" id="reportYearNew">
                        @foreach($reportYears as $year)
                            <option value="{{ $year->id }}">{{ $year->fiscal_year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Award Type</label>
                    <select name="eoyAwardNew" class="form-control" id="eoyAwardNew">
                        @foreach($eoyAwards as $award)
                            <option value="{{ $award->id }}">{{ $award->award_type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Badge Image (PNG)</label>
                    <input type="file" class="form-control" id="fileNameNew" name="fileNameNew" accept=".png">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger bg-gradient mb-2" data-bs-dismiss="modal"><i class="bi bi-x-lg me-2"></i>Close</button>
                <button type="button" class="btn btn-success bg-gradient mb-2" onclick="addAwardBadge()"><i class="bi bi-floppy-fill me-2"></i>Add Badge</button>
            </div>
        </div>
    </div>
</div>
