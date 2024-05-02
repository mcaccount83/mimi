@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Admin
       <small>Toolkit</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Toolkit</li>
      </ol>
    </section>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('fail'))
        <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <p>{{ $message }}</p>
        </div>
    @endif

<!-- Main content -->
<section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box card">
          <div class="box-header with-border">
            <h3 class="box-title">Chapter Resources - Links & File Downloads</h3>
            <h4>Board members have the same list of links & file downloads available through their MIMI logins.</h4>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <!-- /.form group -->
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  @if($canEditFiles)
                      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true"></i>&nbsp; Add Resource</button>
                  @endif
                  <hr>
              </div>
          </div>
      </div>


      <div class="grid">
          <!-- Grid item -->
          <div class="grid-item col-md-4">
              <div class="box">
                  <div class="box-header with-border">
                      <h3 class="box-title">JOB DESCRIPTIONS</h3>
                  </div>
                  <div class="box-body">
                      @foreach($resources->where('category', 9) as $resourceItem)
                      <div class="card">
                          <div class="card-body">
                              <h4>
                                  @if ($resourceItem->link)
                                  <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                  @if($canEditFiles)
                                  <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                  @endif
                                  @elseif ($resourceItem->file_path)
                                  <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                  @if($canEditFiles)
                                  <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                  @endif
                                  @else
                                  {{ $resourceItem->name }}&nbsp;
                                  @if($canEditFiles)
                                  <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                  @endif
                                  @endif
                              </h4>
                          </div>
                      </div>
                      @endforeach
                  </div>
              </div>
          </div>
                            <!-- Grid item -->
            <div class="grid-item col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">NEED BASED FACT SHEETS</h3>
                    </div>
                    <div class="box-body">
                        @foreach($resources->where('category', 8) as $resourceItem)
                        <div class="card">
                            <div class="card-body">
                                <h4>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </h4>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Grid item -->
            <div class="grid-item col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">RESOURCES FOR COORDINATORS</h3>
                    </div>
                    <div class="box-body">
                        @foreach($resources->where('category', 10) as $resourceItem)
                        <div class="card">
                            <div class="card-body">
                                <h4>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </h4>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Grid item -->
            <div class="grid-item col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">RESOURCES FOR CHAPTERS</h3>
                    </div>
                    <div class="box-body">
                        @foreach($resources->where('category', 11) as $resourceItem)
                        <div class="card">
                            <div class="card-body">
                                <h4>
                                    @if ($resourceItem->link)
                                    <a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @elseif ($resourceItem->file_path)
                                    <a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}&nbsp;{{ $resourceItem->version ? '(' . $resourceItem->version . ')' : '' }}</a>
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @else
                                    {{ $resourceItem->name }}&nbsp;
                                    @if($canEditFiles)
                                    <span style="font-size: small;">&nbsp;|&nbsp;<a href="#" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">UPDATE</a></span>
                                    @endif
                                    @endif
                                </h4>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>


            </div>

            <!-- Modal for MIMI Position Abriviations task -->
            <div class="modal fade" id="modal-positions">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h3 class="modal-title"><strong>MIMI Position Abreviations</strong</h3>
                        </div>
                        <div class="modal-body">
                            <table>
                                <tr>
                                  <td><h4>BS</h4></td>
                                  <td><h4>Big Sister</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>AC</h4></td>
                                  <td><h4>Area Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>SC</h4></td>
                                  <td><h4>State Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>ARC</h4></td>
                                  <td><h4>Assistant Regional Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>RC</h4></td>
                                  <td><h4>Regional Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>ACC</h4></td>
                                  <td><h4>Assistant Conference Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>CC</h4></td>
                                  <td><h4>Conference Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>IC</h4></td>
                                  <td><h4>Inquiries Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>WR</h4></td>
                                  <td><h4>Website Reviewer</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>ReReg&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4></td>
                                  <td><h4>Re-registration Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>CDC</h4></td>
                                  <td><h4>Chapter Development Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>VC</h4></td>
                                  <td><h4>Volunteer Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>Corr</h4></td>
                                  <td><h4>Correspondence Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>SMC</h4></td>
                                  <td><h4>Conference Social Media Coordinator</h4></td>
                                </tr>
                                <tr>
                                  <td><h4>SPC</h4></td>
                                  <td><h4>Special Projects Coordinator</h4></td>
                                </tr>
                              </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true" ></i>&nbsp; Close</button>
                        </div>
                    </div>
                </div>
            </div>


            @foreach($resources as $resourceItem)
            <!-- Modal for editing task -->
            <div class="modal fade" id="editResourceModal{{ $resourceItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editResourceModal{{ $resourceItem->id }}Label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h3 class="modal-title" id="#editResourceModal{{ $resourceItem->id }}Label">{{ $resourceItem->name }}</h3>
                            </div>
                        <div class="modal-body">
                            <form>
                                <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fileCategory{{ $resourceItem->id }}">Category</label>
                                    <select name="fileCategory" class="form-control select2" style="width: 50%;" id="fileCategory{{ $resourceItem->id }}" disabled>
                                        <option value="8" {{ $resourceItem->category == 8 ? 'selected' : '' }}>Need Based Fact Sheet</option>
                                        <option value="9" {{ $resourceItem->category == 9 ? 'selected' : '' }}>Job Description</option>
                                        <option value="10" {{ $resourceItem->category == 10 ? 'selected' : '' }}>Resource for Coordinators</option>
                                        <option value="11" {{ $resourceItem->category == 11 ? 'selected' : '' }}>Resource for Chapters</option>

                                    </select>
                                </div>
                                </div>
                                <div class="col-md-12">
                                <div class="form-group">
                                    <label for="fileDescription">Description</label>
                                    <textarea class="form-control" id="fileDescription{{ $resourceItem->id }}">{{ $resourceItem->description }}</textarea>
                                </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="fileType{{ $resourceItem->id }}">File Type</label>
                                        <select class="form-control fileType" id="fileType{{ $resourceItem->id }}" name="fileType">
                                            <option value="1" {{ $resourceItem->file_type == 1 ? 'selected' : '' }}>Document to Download</option>
                                            <option value="2" {{ $resourceItem->file_type == 2 ? 'selected' : '' }}>Link to Webpage</option>
                                        </select>
                                    </div>
                                    <div class="form-group versionField" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                                        <label for="fileVersion{{ $resourceItem->id }}">Version</label>
                                        <input type="text" class="form-control" id="fileVersion{{ $resourceItem->id }}" name="fileVersion" value="{{ $resourceItem->version }}">
                                    </div>
                                    <div class="form-group filePathField" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                                        File Path: <a href="{{ $resourceItem->file_path }}">{{ $resourceItem->file_path }}</a>
                                    </div>
                                    <div class="form-group linkField" style="{{ $resourceItem->file_type === 2 ? 'display:block;' : 'display:none;' }}">
                                        <label for="link{{ $resourceItem->id }}">Link</label>
                                        <input type="text" class="form-control" id="link{{ $resourceItem->id }}" name="link" value="{{ $resourceItem->link }}">
                                    </div>
                                </div>
                            <div class="col-md-12">
                                <div class="form-group fileUpload" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                                    <input type="file" id="fileUpload{{ $resourceItem->id }}" class="form-control" name='fileUpload' required>
                                </div>
                            </div>
                        </form>
                                <div class="col-md-12"><br></div>
                                <div class="col-md-12">
                                <div class="form-group">
                                    Updated by <strong>{{ $resourceItem->updated_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($resourceItem->updated_date)->format('m-d-Y') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true" ></i>&nbsp; Close</button>
                            <button type="button" class="btn btn-success" onclick="updateFile({{ $resourceItem->id }})"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for adding task -->
             <div class="modal fade" id="modal-task">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Add a New Resource</h4>
                            </div>
                            <div class="modal-body">
                                <form id="addResourceForm">
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select name="fileCategoryNew" class="form-control select2" style="width: 50%;" id="fileCategoryNew" >
                                            <option value="8" >Need Based Fact Sheet</option>
                                            <option value="9" >Job Description</option>
                                            <option value="10" >Resource for Coordinators</option>
                                            <option value="11" >Resource for Chapters</option>
                                        </select>
                                        </div>
                                    <div class="form-group">
                                        <label for="fileNameNew">Name</label>
                                        <input type="text" class="form-control" id="fileNameNew" name="fileNameNew">
                                    </div>

                                    <div class="form-group">
                                        <label for="fileDetailsNew">Description</label>
                                        <textarea class="form-control" id="fileDescriptionNew" name="fileDescriptionNew"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="fileTypeNew">File Type</label>
                                        <select class="form-control" id="fileTypeNew" name="fileTypeNew">
                                            <option value="" selected>Select file type</option>
                                            <option value="1">Document to Download</option>
                                            <option value="2">Link to Webpage</option>
                                        </select>

                                    </div>
                                    <div class="form-group versionFieldNew" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                                        <label for="fileVersionNew">Version</label>
                                        <input type="text" class="form-control" id="fileVersionNew" name="fileVersionNew">
                                    </div>
                                    <div class="form-group linkFieldNew" style="{{ $resourceItem->file_type === 2 ? 'display:block;' : 'display:none;' }}">
                                        <label for="linkNew">Link</label>
                                        <input type="text" class="form-control" id="linkNew" name="linkNew" >
                                    </div>
                                    <div class="form-group fileUploadNew" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                                        <input type="file" id="fileUploadNew" class="form-control" name="fileUploadNew" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true" ></i>&nbsp; Close</button>
                                <button type="button" class="btn btn-success" onclick="return addFile()"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Add Resource</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            </section>
            <!-- /.content -->
            @endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js"></script>

<script>
$(document).ready(function() {
    var elem = document.querySelector('.grid');
    var msnry = new Masonry(elem, {
        itemSelector: '.grid-item',
        columnWidth: '.grid-item', // Set column width to grid-item for consistent size
        //gutter: 20, // Set gutter for spacing between items
        percentPosition: true
    });
});
</script>
<script>
    $(document).ready(function() {
        $('.fileType').change(function() {
            var selectedType = $(this).val();
            var resourceId = $(this).attr('id').replace('fileType', '');

            if (selectedType === '1') {
                $('.versionField').show();
                $('.filePathField').show();
                $('.fileUpload').show();
                $('.filePathBlock').show();
                $('.linkField').hide();
            } else if (selectedType === '2') {
                $('.linkField').show();
                $('.versionField').hide();
                $('.filePathField').hide();
                $('.filePathBlock').hide();
                $('.fileUpload').hide();
            }
        });

        $('.linkFieldNew').hide();
        $('.versionFieldNew').hide();
        $('.fileUploadNew').hide();

        $('#fileTypeNew').change(function() {
            var selectedType = $(this).val();

            if (selectedType === '1') {
                $('.versionFieldNew').show();
                $('.fileUploadNew').show();
                $('.linkFieldNew').hide();
            } else if (selectedType === '2') {
                $('.linkFieldNew').show();
                $('.versionFieldNew').hide();
                $('.fileUploadNew').hide();
            }
        });
    });

    function addFile() {
    var fileCategoryNew = document.getElementById('fileCategoryNew').value;
    var fileNameNew = document.getElementById('fileNameNew').value;
    var fileDescriptionNew = document.getElementById('fileDescriptionNew').value;
    var fileTypeNew = document.getElementById('fileTypeNew').value;
    var fileVersionNew = document.getElementById('fileVersionNew').value;
    var linkNew = document.getElementById('linkNew').value;

    if (fileCategoryNew == '') {
        alert('Category is Required.');
        return false; // Prevent form submission
    }
    if (fileTypeNew == '') {
        alert('File Type is Required.');
        return false; // Prevent form submission
    }
    if (fileNameNew == '') {
        alert('Toolkit Name is Required.');
        return false; // Prevent form submission
    }
    if (fileNameNew.length > 50) {
        alert('Name cannot exceed 50 characters.');
        return false; // Prevent form submission
    }
    if (fileDescriptionNew == '') {
        alert('Toolkit Description is Required.');
        return false; // Prevent form submission
    }
    if (fileDescriptionNew.length > 500) {
        alert('Description cannot exceed 500 characters.');
        return false; // Prevent form submission
    }
    if (fileTypeNew === '2') {
            if (linkNew == '') {
            alert('Toolkit Link is Required.');
            return false; // Prevent form submission
        }
    }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Prepare the data to send
    var data = {
        fileCategoryNew: fileCategoryNew,
        fileNameNew: fileNameNew,
        fileDescriptionNew: fileDescriptionNew,
        fileTypeNew: fileTypeNew
    };

    if (fileTypeNew === '1') { // Add version and link fields if file type is 1 (Document to Download)
        data.fileVersionNew = fileVersionNew;
    } else if (fileTypeNew === '2') { // Add file path if file type is 2 (Link to Webpage)
        data.LinkNew = linkNew;
    }

    // Send an AJAX request to Laravel backend to create a new toolkit
    $.ajax({
    url: '{{ route('admin.addtoolkit') }}',
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': csrfToken
    },
    data: data,
    success: function(response) {
        // Extract the newly created id from the response
        var id = response.id;
        var fileType = response.file_type;

        // Now, you have the id, you can proceed to upload the file to Google Drive
        // Construct the FormData object to send
        if (fileType === '1') {
        var formData = new FormData();
        formData.append('file', document.getElementById('fileUploadNew').files[0]);

        // Send an AJAX request to upload the file to Google Drive
        $.ajax({
            url: '{{ route('store.toolkit', '') }}' + '/' + id,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                alert('Toolkit & File added successfully');
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle error if needed
                console.error(xhr.responseText);
            }
        });
    } else {
            alert('Toolkit added successfully');
            location.reload();
        }
    },
    error: function(xhr, status, error) {
        // Handle error
        console.error(xhr.responseText);
    }
});

    // Close the modal
    $('#modal-task').modal('hide');

    // Prevent form submission
    return false;
}


function updateFile(id) {
    var file = document.getElementById('fileUpload' + id).files[0];
    var fileDescription = document.getElementById('fileDescription' + id).value;
    var fileType = document.getElementById('fileType' + id).value;
    var fileVersion = document.getElementById('fileVersion' + id).value;
    var link = document.getElementById('link' + id).value;

    var formData = new FormData();
    formData.append('file', file);
    formData.append('fileDescription', fileDescription);
    formData.append('fileType', fileType);
    formData.append('fileVersion', fileVersion);
    formData.append('link', link);

    if (fileDescription == '') {
        alert('Toolkit Description is Required.');
        return false; // Prevent form submission
    }
    if (fileDescription.length > 500) {
        alert('Description cannot exceed 500 characters.');
        return false; // Prevent form submission
    }
    if (fileType === '2') {
            if (link == '') {
            alert('Toolkit Link is Required.');
            return false; // Prevent form submission
        }
    }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send an AJAX request to update the toolkit
    $.ajax({
        url: '{{ route('admin.updatetoolkit', '') }}' + '/' + id,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {

    // Send an AJAX request to upload the file to Google Drive
    if (fileType === '1') {
        $.ajax({
            url: '{{ route('store.toolkit', '') }}' + '/' + id,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                alert('Toolkit & File updated successfully');
                location.reload();
            },
            error: function(xhr, status, error) {
                // Handle error if needed
                console.error(xhr.responseText);
            }
        });
    } else {
            alert('Toolkit updated successfully');
            location.reload();
        }
    },
    error: function(xhr, status, error) {
        // Handle error
        console.error(xhr.responseText);
    }
});

    // Close the modal
    $('#editResourceModal' + id).modal('hide');

    // Prevent form submission
    return false;
}

</script>
</html>
