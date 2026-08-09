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
                                        $sendUrl = $campaign->send_url ?? '#';
                                        $previewUrl = $campaign->preview_url;
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
                                            @if(!empty($campaign->attachments))
                                                {{ implode(', ', $campaign->attachments) }}
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
@include('coordinators.partials.email_preview_modal')

<!-- Add/Edit Campaign Modal -->
<div class="modal fade" id="campaignModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="campaignModalTitle">Add Campaign</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 0.85rem;">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Reminder:</strong> Adding a campaign here only creates the database entry and send buttons in MIMI. The email template, send route/controller logic,
            and any attachments must still be manually coded before the campaign will actually work. Start with <code>EmailCampaignController.php</code>.
        </div>
        <form id="campaignForm">
            <input type="hidden" id="campaignId" value="">
            <div class="mb-3">
                <label class="form-label">Campaign Key</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="campaignKey" oninput="updateCampaignPreview()">
                    <span class="input-group-text">Campaign</span>
                    <div class="text-muted" style="font-size: 0.8rem;">This is the unique campaign key. There should be no spaces and each word should be capatalized (ex: BudgetMeeting).</div>
                </div>
                <div class="text-muted mt-1" style="font-size: 0.85rem;">
                    <div>Route: <span id="previewRoute"></span></div>
                    <div>Slug: <span id="previewSlug"></span></div>
                    <div>Confirm Fn: <span id="previewConfirmFn"></span></div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Campaign Title</label>
                <input type="text" class="form-control" id="campaignLabel">
                <div class="text-muted" style="font-size: 0.8rem;">This is the title of the campaign as it will appear in lists and on buttons (ex: The Executive Board).</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Month to Send</label>
                <select class="form-select" id="campaignMonth">
                    <option value="">—</option>
                    @foreach($monthNames as $m => $name)
                        <option value="{{ $m }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" class="form-check-input" id="campaignUsesConfirmFn" onchange="updateCampaignPreview()">
                <label class="form-check-label" for="campaignUsesConfirmFn">Uses Custom Confirm Function</label>
                <div class="text-muted" style="font-size: 0.8rem;">Only check this if the campaign needs extra input (ex: Holiday Break's break dates). You'll need to hand-write a matching <code>confirmSend&lt;Key&gt;</code> JS function, or the Send button will error.</div>
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" class="form-check-input" id="campaignHasAttachments" onchange="toggleAttachmentsField()">
                <label class="form-check-label" for="campaignHasAttachments">Has Attachments</label>
            </div>
            <div class="mb-3" id="campaignAttachmentsWrapper" style="display: none;">
                <label class="form-label">Attachments (comma-separated filenames)</label>
                <input type="text" class="form-control" id="campaignAttachments">
                <div class="text-muted" style="font-size: 0.8rem;">This is just a list of attachments, it does not connect the actual attahments themselves. List as comma-separated filenames (ex: Electon Timeline.pdf).</div>
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

function deriveCampaignFields(key) {
    const slug = key.replace(/(?!^)([A-Z])/g, '-$1').toLowerCase();
    return {
        campaign: key + 'Campaign',
        route_name: 'campaigns.send' + key.toLowerCase(),
        preview_slug: slug,
    };
}

function updateCampaignPreview() {
    const key = document.getElementById('campaignKey').value.trim();
    const usesConfirmFn = document.getElementById('campaignUsesConfirmFn').checked;

    if (!key) {
        document.getElementById('previewRoute').textContent = '—';
        document.getElementById('previewSlug').textContent = '—';
        document.getElementById('previewConfirmFn').textContent = '—';
        return;
    }

    const derived = deriveCampaignFields(key);
    document.getElementById('previewRoute').textContent = derived.route_name;
    document.getElementById('previewSlug').textContent = derived.preview_slug;
    document.getElementById('previewConfirmFn').textContent = usesConfirmFn ? ('confirmSend' + key) : 'none';
}

function toggleAttachmentsField() {
    const checked = document.getElementById('campaignHasAttachments').checked;
    const wrapper = document.getElementById('campaignAttachmentsWrapper');
    wrapper.style.display = checked ? 'block' : 'none';
    if (!checked) {
        document.getElementById('campaignAttachments').value = '';
    }
}

function openCampaignModal() {
    document.getElementById('campaignForm').reset();
    document.getElementById('campaignId').value = '';
    toggleAttachmentsField();
    updateCampaignPreview();
    document.getElementById('campaignModalTitle').textContent = 'Add Campaign';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('campaignModal')).show();
}

function editCampaign(id) {
    const c = campaignsData.find(c => c.id === id);
    if (!c) return;

    const key = c.campaign.replace(/Campaign$/, '');

    document.getElementById('campaignId').value = c.id;
    document.getElementById('campaignKey').value = key;
    document.getElementById('campaignLabel').value = c.label ?? '';
    document.getElementById('campaignMonth').value = c.month ?? '';
    document.getElementById('campaignUsesConfirmFn').checked = !!c.confirm_fn;
    document.getElementById('campaignHasAttachments').checked = !!(c.attachments && c.attachments.length);
    document.getElementById('campaignAttachments').value = c.attachments ? c.attachments.join(', ') : '';
    document.getElementById('campaignActive').checked = !!c.active;
    toggleAttachmentsField();
    updateCampaignPreview();
    document.getElementById('campaignModalTitle').textContent = 'Edit Campaign';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('campaignModal')).show();
}

function saveCampaign() {
    const id = document.getElementById('campaignId').value;
    const key = document.getElementById('campaignKey').value.trim();

    if (!key) {
        Swal.fire('Error', 'Campaign Key is required.', 'error');
        return;
    }

    const derived = deriveCampaignFields(key);
    const usesConfirmFn = document.getElementById('campaignUsesConfirmFn').checked;
    const hasAttachments = document.getElementById('campaignHasAttachments').checked;

    const payload = {
        campaign: derived.campaign,
        label: document.getElementById('campaignLabel').value,
        month: document.getElementById('campaignMonth').value,
        route_name: derived.route_name,
        confirm_fn: usesConfirmFn ? ('confirmSend' + key) : null,
        preview_slug: derived.preview_slug,
        attachments: hasAttachments ? document.getElementById('campaignAttachments').value : '',
        active: document.getElementById('campaignActive').checked,
    };

    const url = id
        ? `{{ url('/adminreports/updateemailcampaign') }}/${id}`
        : `{{ route('adminreports.addemailcampaign') }}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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
