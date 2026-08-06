@extends('layouts.mimi_theme')

@section('page_title', 'Admin Reports')
@section('breadcrumb', 'Email Campaigns')

@section('content')
     <!-- Main content -->
     <section class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex align-items-center">
                    <div class="dropdown">
                        <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Email Campaigns
                        </h3>
                        <span class="ms-3">Suggested Month to Send</span>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ms-auto" onclick="openCampaignModal()">
                        <i class="bi bi-plus-lg"></i> Add Campaign
                    </button>
                </div>
                <!-- /.card-header -->
            <div class="card-body">
                <table id="chapterlist" class="table table-sm table-hover" >
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Preview</th>
                            <th>Month</th>
                            <th>Campaign</th>
                            <th>Attachments</th>
                            <th>Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthNames as $m => $name)
                            @if($m == 7)
                                <tr>
                                    <td class="text-center align-middle text-muted">
                                        {{-- <i class="bi bi-envelope text-primary"></i> --}}
                                    </td>
                                    <td> </td>
                                    <td>{{ $name }}</td>
                                    <td>Thank You Old Board & Welcome New Board<br>
                                        <small class="fst-italic text-muted">Auto-sends when new board is activated</small></td>
                                    <td>Officer Packet.pdf</td>
                                    <td></td>
                                </tr>
                            @elseif(isset($campaigns[$m]))
                                @foreach($campaigns[$m] as $campaign)
                                   @php
                                        $fn = $campaign->confirm_fn ?: 'confirmSendCampaign';
                                        $sendUrl = \Illuminate\Support\Facades\Route::has($campaign->route_name) ? route($campaign->route_name) : '#';
                                        $previewUrl = $campaign->preview_slug ? route('campaigns.preview', $campaign->preview_slug) : null;
                                        $attachmentList = $campaign->attachments ?? [];
                                    @endphp
                                    <tr data-id="{{ $campaign->id }}">
                                        <td class="text-center align-middle">
                                            <a onclick="{{ $fn }}('{{ $campaign->label }}', '{{ $sendUrl }}')" style="cursor: pointer;">
                                                <i class="bi bi-envelope text-primary"></i>
                                            </a>
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($previewUrl)
                                                <a onclick="event.stopPropagation(); previewCampaign('{{ $previewUrl }}', '{{ $campaign->label }}')" style="cursor: pointer;" class="ms-2">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                        <td>{{ $name }}</td>
                                        <td>{{ $campaign->label }}</td>
                                        <td>
                                            @if(!empty($attachmentList))
                                                {{ implode(', ', $attachmentList) }}
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <a onclick="event.stopPropagation(); editCampaign({{ $campaign->id }})" style="cursor: pointer;">
                                                <i class="bi bi-pencil text-secondary"></i>
                                            </a>
                                            <a onclick="event.stopPropagation(); deleteCampaign({{ $campaign->id }})" style="cursor: pointer;" class="ms-2">
                                                <i class="bi bi-trash text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td>{{ $name }}</td>
                                    <td class="text-muted fst-italic"><small>No campaign</small></td>
                                    <td> </td>
                                    <td></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->

        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection

<!-- Email Preview Modal -->
<div class="modal fade" id="emailPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="emailPreviewLabel">Email Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="emailPreviewFrame" style="width:100%; height:75vh; border:0;"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Campaign Modal -->
<div class="modal fade" id="campaignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="campaignModalTitle">Add Campaign</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="campaignForm">
            <input type="hidden" id="campaignId" value="">
            <div class="mb-3">
                <label class="form-label">Campaign Key</label>
                <input type="text" class="form-control" id="campaignKey" placeholder="BudgetMeetingCampaign">
            </div>
            <div class="mb-3">
                <label class="form-label">Label</label>
                <input type="text" class="form-control" id="campaignLabel" placeholder="The Executive Board">
            </div>
            <div class="mb-3">
                <label class="form-label">Month</label>
                <select class="form-select" id="campaignMonth">
                    <option value="">—</option>
                    @foreach($monthNames as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Route Name</label>
                <input type="text" class="form-control" id="campaignRoute" placeholder="campaigns.sendbudgetmeeting">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Function (optional)</label>
                <input type="text" class="form-control" id="campaignConfirmFn" placeholder="confirmSendHolidayBreak">
            </div>
            <div class="mb-3">
                <label class="form-label">Preview Slug</label>
                <input type="text" class="form-control" id="campaignPreviewSlug" placeholder="budget-meeting">
            </div>
            <div class="mb-3">
                <label class="form-label">Attachments (comma-separated filenames)</label>
                <input type="text" class="form-control" id="campaignAttachments" placeholder="Election Timetable.pdf">
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="campaignActive" checked>
                <label class="form-check-label" for="campaignActive">Active</label>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveCampaign()">Save</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const campaignsData = @json($campaigns->flatten());

function previewCampaign(url, label) {
    document.getElementById('emailPreviewLabel').innerText = label + ' — Preview';
    const frame = document.getElementById('emailPreviewFrame');
    frame.src = '';

    fetch(url)
        .then(res => res.text())
        .then(html => {
            frame.srcdoc = html;
            new bootstrap.Modal(document.getElementById('emailPreviewModal')).show();
        })
        .catch(() => {
            Swal.fire('Error', 'Could not load email preview.', 'error');
        });
}

function openCampaignModal() {
    document.getElementById('campaignForm').reset();
    document.getElementById('campaignId').value = '';
    document.getElementById('campaignModalTitle').textContent = 'Add Campaign';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('campaignModal')).show();
}

function editCampaign(id) {
    const c = campaignsData.find(c => c.id === id);
    if (!c) return;

    document.getElementById('campaignId').value = c.id;
    document.getElementById('campaignKey').value = c.campaign;
    document.getElementById('campaignLabel').value = c.label ?? '';
    document.getElementById('campaignMonth').value = c.month ?? '';
    document.getElementById('campaignRoute').value = c.route_name;
    document.getElementById('campaignPreviewSlug').value = c.preview_slug ?? '';
    document.getElementById('campaignAttachments').value = c.attachments ? c.attachments.join(', ') : '';
    document.getElementById('campaignActive').checked = !!c.active;
    document.getElementById('campaignModalTitle').textContent = 'Edit Campaign';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('campaignModal')).show();
}

function saveCampaign() {
    const id = document.getElementById('campaignId').value;
    const payload = {
        campaign: document.getElementById('campaignKey').value,
        label: document.getElementById('campaignLabel').value,
        month: document.getElementById('campaignMonth').value,
        route_name: document.getElementById('campaignRoute').value,
        preview_slug: document.getElementById('campaignPreviewSlug').value,
        attachments: document.getElementById('campaignAttachments').value,
        active: document.getElementById('campaignActive').checked,
    };

    const url = id
        ? `{{ url('/adminreports/updateemailcampaign') }}/${id}`
        : `{{ route('adminreports.addemailcampaign') }}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(payload),
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                Swal.fire('Error', data.message ?? 'Something went wrong.', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Could not save campaign.', 'error');
        });
}

function deleteCampaign(id) {
    Swal.fire({
        title: 'Delete this campaign?',
        text: 'This cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
    }).then((result) => {
        if (!result.isConfirmed) return;

        fetch(`{{ route('adminreports.deleteemailcampaign') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ campaignId: id }),
        })
            .then(res => res.json())
            .then(() => location.reload())
            .catch(() => {
                Swal.fire('Error', 'Could not delete campaign.', 'error');
            });
    });
}
</script>
@endpush
