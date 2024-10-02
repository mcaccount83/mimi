@extends('layouts.coordinator_theme')

@section('content')
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Report Attachment Update&nbsp;<small>(Edit)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Report Attachment Update</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" action='{{ route("chapter.updateattachments",$chapterList[0]->id) }}'>
    @csrf
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Chapter</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-sm-4">
              <div class="form-group">
                <label>MOMS Club of</label>
                <input type="text" name="ch_name" class="form-control" maxlength="200" required value="{{ $chapterList[0]->name }}" onchange="PreviousNameReminder()" disabled>
              </div>
              </div>
              <!-- /.form group -->
            <div class="col-sm-4">
              <div class="form-group">
                <label>State</label>
                <select id="ch_state" name="ch_state" class="form-control select2-bs4" style="width: 100%;" required disabled>
                  <option value="">Select State</option>
                    @foreach($stateArr as $state)
                      <option value="{{$state->id}}" {{$chapterList[0]->state == $state->id  ? 'selected' : ''}}>{{$state->state_long_name}}</option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_state" value="{{ $chapterList[0]->state }}">
              </div>
              </div>
              <div class="col-sm-4">
              <div class="form-group">
                <label>Region</label> <span class="field-required">*</span>
                <select id="ch_region" name="ch_region" class="form-control select2-bs4" style="width: 100%;" required disabled>
                  <option value="">Select Region</option>
                    @foreach($regionList as $rl)
                      <option value="{{$rl->id}}" {{$chapterList[0]->region == $rl->id  ? 'selected' : ''}} >{{$rl->long_name}} </option>
                    @endforeach
                </select>
                <input type="hidden" name="ch_hid_region" value="{{ $chapterList[0]->region }}">
              </div>
              </div>
              </div>
            </div>


                    <div class="card-header">
                        <h3 class="card-title">Chapter Roster</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
              <div class="col-md-12">

                @if (!empty($chapterList[0]->roster_path))
                            <div class="col-12">
                                <label>Chapter Roster File:</label><a href="https://drive.google.com/uc?export=download&id={{ $chapterList[0]->roster_path }}">&nbsp; Chapter Roster</a><br>
                            </div>
                            <div class="col-12" id="RosterBlock">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Roster File</button>
                        </div>
                    @else
                        <div class="col-12">
                            <label class="control-label" for="RosterLink">Chapter Roster File:</label>
                            No file attached
                        </div>
                        <div class="col-12" id="RosterBlock">
                            <button type="button" class="btn btn-sm btn-primary" onclick="showRosterUploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Roster File</button>
                        </div>
                    @endif
                    </div>
                </div>
            </div>

            <div class="card-header">
                <h3 class="card-title">Bank Statements</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
      <!-- /.form group -->
      <div class="col-md-12">
                    @if (!empty($chapterList[0]->bank_statement_included_path))
                        <div class="col-12">
                            <label>Primary Bank Statement:</label><a href="https://drive.google.com/uc?export=download&id={{ $chapterList[0]->bank_statement_included_path }}" >&nbsp; View Bank Statement</a><br>
                        </div>
                        @else
                        <div class="col-12">
                            <label class="control-label" for="StatementLink">Primary Bank Statement:</label>
                            No file attached
                        </div>
                    @endif
                    @if (!empty($chapterList[0]->bank_statement_2_included_path))
                    <div class="col-12">
                            <label>Additional Bank Statement:</label><a href="https://drive.google.com/uc?export=download&id={{ $chapterList[0]->bank_statement_2_included_path }}" >&nbsp; View Additional Bank Statement</a><br>
                        </div>
                        @else
                        <div class="col-12">
                            <label class="control-label" for="Statement2Link">Additional Bank Statement:</label>
                            No file attached
                        </div>
                    @endif
                    <div class="col-12" id="StatementBlock">
                            @if (!empty($chapterList[0]->bank_statement_included_path))
                            <button type="button" class="btn btn-sm btn-primary mb-3" onclick="showStatement1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Bank Statement</button>
                        @else
                            <button type="button" class="btn btn-sm btn-primary mb-3" onclick="showStatement1UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Bank Statement</button>
                        @endif
                    </div>
                    <div class="col-12" id="Statement2Block">
                        @if (!empty($chapterList[0]->bank_statement_2_included_path))
                        <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace Additional Bank Statement</button>
                        @else
                            <button type="button" class="btn btn-sm btn-primary" onclick="showStatement2UploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload Additional Bank Statement</button>
                        @endif
                    </div>
              </div>
            </div>
        </div>

            <div class="card-header">
                <h3 class="card-title">990N Filing Confirmation</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="row">
      <!-- /.form group -->
      <div class="col-md-12">

        @if (!empty($chapterList[0]->file_irs_path))
            <div class="col-12">
                <label>990N Filing Uploaded:</label><a href="https://drive.google.com/uc?export=download&id={{ $chapterList[0]->file_irs_path }}">&nbsp; View 990N Confirmation</a><br>
            </div>
        @else
            <div class="col-12">
                <label class="control-label" for="990NLink">990N Filing:</label>
                No file attached
            </div>
        @endif
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <label class="mr-2" for="irs_verified">990N Filing Verified on IRS Website</label> <!-- Label in front -->
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="irs_verified" id="irs_verified" class="custom-control-input" {{ $chapterList[0]->check_current_990N_verified_IRS ? 'checked' : '' }} />
                        <label class="custom-control-label" for="irs_verified"></label> <!-- Empty label for the switch itself -->
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                  <label>990N Filing Notes:</label>
                  <input type="text" name="irs_notes" id="irs_notes" class="form-control" maxlength="50" value="{{$chapterList[0]->check_current_990N_notes}}">
                </div>
                </div>

                <div class="col-12">

                <button type="submit" class="btn btn-sm btn-primary mb-3"><i class="fas fa-save" ></i>&nbsp; Save 990N Information</button>
                </div>

        @if (!empty($chapterList[0]->file_irs_path))
            <div class="col-12" id="990NBlock">

                <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()"><i class="fas fa-upload"></i>&nbsp; Replace 990N Confirmation</button>
            </div>
        @else
            <div class="col-12" id="990NBlock">

                <button type="button" class="btn btn-sm btn-primary" onclick="show990NUploadModal()"><i class="fas fa-upload"></i>&nbsp; Upload 990N Confirmation</button>
            </div>
        @endif

        </div>
    </div>
    </div>
                    </div>
                </div>
            </div>

    </div>
    </section>
    </form>
    @endsection

    @section('customscript')
    <script>

    function showRosterUploadModal() {
    var chapter_id = "{{ $chapterList[0]->id }}";

    Swal.fire({
        title: 'Upload Chapter Roster',
        html: `
            <form id="uploadRosterForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadRosterForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeRoster', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Roster uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function showStatement1UploadModal() {
    var chapter_id = "{{ $chapterList[0]->id }}";

    Swal.fire({
        title: 'Upload Statement',
        html: `
            <form id="uploadStatement1Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement1Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement1', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Statement uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function showStatement2UploadModal() {
    var chapter_id = "{{ $chapterList[0]->id }}";

    Swal.fire({
        title: 'Upload Additional Statement',
        html: `
            <form id="uploadStatement2Form" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('uploadStatement2Form'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/storeStatement2', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Additional Statement uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

function show990NUploadModal() {
    var chapter_id = "{{ $chapterList[0]->id }}";

    Swal.fire({
        title: 'Upload 990N',
        html: `
            <form id="upload990NForm" enctype="multipart/form-data">
                @csrf
                <input type="file" name='file' required>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Close',
        preConfirm: () => {
            var formData = new FormData(document.getElementById('upload990NForm'));

            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we upload your file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();

                    $.ajax({
                        url: '{{ url('/files/store990n', '') }}' + '/' + chapter_id,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: 'Success!',
                                text: '990N uploaded successfully!',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload(); // Reload the page to reflect changes
                            });
                        },
                        error: function(jqXHR, exception) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong, please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });

            return false;
        },
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    });
}

        </script>

        @endsection

