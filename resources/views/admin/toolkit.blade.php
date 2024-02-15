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
              <h3 class="box-title">Coordinator Toolkit - Links & File Downloads</h3>
              <h4>Resources for Coordinators.</h4>
            </div>

            <!-- /.box-header -->
            <div class="box-body">
              <!-- /.form group -->
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Toolkit Item</button>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">JOB DESCRIPTIONS</h3>
                                </div>
                                <div class="box-body">
                                        @foreach($resources->where('category', 5) as $resourceItem)
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="col-md-12">
                                                            @if ($resourceItem->link)
                                                            <h4><a href="{{ $resourceItem->link }}" target="_blank">{{ $resourceItem->name }}</a> (<a href="#" data-toggle="modal" data-target="#modal-task">Update</a>)</h4>
                                                            @elseif ($resourceItem->file_path)
                                                            <h4><a href="{{ $resourceItem->file_path }}" target="_blank">{{ $resourceItem->name }}</a> (<a href="#" data-toggle="modal" data-target="#modal-task">Update</a>)</h4>
                                                            @else
                                                            <h4>{{ $resourceItem->name }} (<a href="#" data-toggle="modal" data-target="#modal-task">Update</a>)</h4>
                                                            @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal for editing task -->
                                            <div class="modal fade" id="editByLawsModal{{ $resourceItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editByLawsModal{{ $resourceItem->id }}Label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span></button>
                                                                <h3 class="modal-title" id="#editByLawsModal{{ $resourceItem->id }}Label">{{ $resourceItem->name }}</h3>
                                                            </div>
                                                        <div class="modal-body">
                                                            <form>
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
                                                                            <option value="1" {{ $resourceItem->file_type === 1 ? 'selected' : '' }}>Document to Download</option>
                                                                            <option value="2" {{ $resourceItem->file_type === 2 ? 'selected' : '' }}>Link to Webpage</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group versionField" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                                                                        <label for="fileVersion{{ $resourceItem->id }}">Version</label>
                                                                        <input type="text" class="form-control" id="fileVersion{{ $resourceItem->id }}" name="fileVersion" value="{{ $resourceItem->version }}">
                                                                    </div>
                                                                    <div class="form-group filePathField" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                                                                        <label for="filePath{{ $resourceItem->id }}">File Path</label>
                                                                        <input type="text" class="form-control" id="filePath{{ $resourceItem->id }}" name="filePath" value="{{ $resourceItem->file_path }}">
                                                                    </div>
                                                                    <div class="form-group linkField" style="{{ $resourceItem->file_type === 2 ? 'display:block;' : 'display:none;' }}">
                                                                        <label for="link{{ $resourceItem->id }}">Link</label>
                                                                        <input type="text" class="form-control" id="link{{ $resourceItem->id }}" name="link" value="{{ $resourceItem->link }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6"><br></div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    Updated <strong>{{ $resourceItem->updated_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($resourceItem->updated_date)->format('m-d-Y') }}</strong>
                                                                </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6"><br></div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true" ></i>&nbsp; Close</button>
                                                            <button type="button" class="btn btn-success" onclick="updateFile({{ $resourceItem->id }})"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save changes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                </div>    {{-- END ITEM --}}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">HELPFUL LINKS FOR COORDINATORS</h3>
                                </div>
                                    <div class="box-body">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="col-md-12">
                                                    <h4><a href="https://momsclub.org/resources/volunteer-application/" target="_blank">Volunteer Application</a></h4>
                                                    <h4><a href="#" data-toggle="modal" data-target="#modal-positions">MIMI Position Abbreviations</a></h4>
                                                    <h4><a href="https://momsclub.org/elearning/" target="_blank">eLearning Library</a></h4>


                                                    <br>
                                                </div>
                                                <div class="col-md-12"><br></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">HELPFUL LNKS FOR CHAPTERS</h3>
                                </div>
                                <div class="box-body">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                <h4><a href="{{ route('admin.resources')}}">Chapter Resources & Fact Sheets</a></h4>
                                                <h4><a href="https://momsclub.org/elearning/" target="_blank">eLearning Library</a></h4>
                                                <h4><a href="https://momsclub.org/store/" target="_blank">MOMS Club Merchandise</a></h4>
                                    <br>
                                </div>
                            </div>
                        </div>
                </div>    {{-- END ROW --}}


            </div>
            </div>
            </div>

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
                                <option value="1" >Job Description</option>
                                <option value="2" >Fact Sheets</option>
                                <option value="3" >End of Year Review</option>
                                <option value="4" >Chapter Resources</option>
                                <option value="5" >Chapter Resources</option>
                                <option value="6" >Sample Chapter Files</option>
                                <option value="7" >End of Year</option>
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
                        <div class="form-group filePathFieldNew" style="{{ $resourceItem->file_type === 1 ? 'display:block;' : 'display:none;' }}">
                            <label for="filePathNew">File Path</label>
                            <input type="text" class="form-control" id="filePathNew" name="filePathNew">
                        </div>
                        <div class="form-group linkFieldNew" style="{{ $resourceItem->file_type === 2 ? 'display:block;' : 'display:none;' }}">
                            <label for="linkNew">Link</label>
                            <input type="text" class="form-control" id="linkNew" name="linkNew" >
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

        </section>
        <!-- /.content -->

    @endsection

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
    // Listen for change event on the dropdown
        $('.fileType').change(function() {
            var selectedType = $(this).val();
            var resourceId = $(this).attr('id').replace('fileType', '');

            // Hide all fields initially
            $('.versionField').hide();
            $('.filePathField').hide();
            $('.linkField').hide();

            // Show the appropriate fields based on the selected value
            if (selectedType === '1') {
                $('#fileVersion' + resourceId).closest('.versionField').show();
                $('#filePath' + resourceId).closest('.filePathField').show();
            } else if (selectedType === '2') {
                $('#link' + resourceId).closest('.linkField').show();
            }
        });
    });

    $(document).ready(function() {
        // Hide link field by default
        $('.linkFieldNew').hide();

        // Listen for change event on the dropdown
        $('#fileTypeNew').change(function() {
            var selectedType = $(this).val();

            // Hide all fields initially
            $('.versionFieldNew').hide();
            $('.filePathFieldNew').hide();
            $('.linkFieldNew').hide();

            // Show the appropriate fields based on the selected value
            if (selectedType === '1') {
                $('.versionFieldNew').show();
                $('.filePathFieldNew').show();
            } else if (selectedType === '2') {
                $('.linkFieldNew').show();
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
    var filePathNew = document.getElementById('filePathNew').value;

    if (fileCategoryNew == '') {
        alert('Category is Required.');
        return false; // Prevent form submission
    }
    if (fileTypeNew == '') {
        alert('File Type is Required.');
        return false; // Prevent form submission
    }
    if (fileNameNew == '') {
        alert('Resource Name is Required.');
        return false; // Prevent form submission
    }
    if (fileNameNew.length > 50) {
        alert('Name cannot exceed 50 characters.');
        return false; // Prevent form submission
    }
    if (fileDescriptionNew == '') {
        alert('Resource Description is Required.');
        return false; // Prevent form submission
    }
    if (fileDescriptionNew.length > 255) {
        alert('Description cannot exceed 255 characters.');
        return false; // Prevent form submission
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
            data.filePathNew = filePathNew;
    } else if (fileTypeNew === '2') { // Add file path if file type is 2 (Link to Webpage)
            data.LinkNew = linkNew;
    }

    // Send an AJAX request to Laravel backend to create a new task
    $.ajax({
        url: '{{ route('admin.addresources') }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: data,
        success: function(response) {
            // Display success message
            alert('Resource added successfully');
            // Reload the page to reflect the new data
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(xhr.responseText);
        }
    });
}

function updateFile(id) {
    console.log('fileDescription:', document.getElementById('fileDescription' + id));
    console.log('fileType:', document.getElementById('fileType' + id));
    console.log('fileVersion:', document.getElementById('fileVersion' + id));
    console.log('link:', document.getElementById('link' + id));
    console.log('filePath:', document.getElementById('filePath' + id));

    var fileDescription = document.getElementById('fileDescription' + id).value;
    var fileType = document.getElementById('fileType' + id).value;
    var fileVersion = document.getElementById('fileVersion' + id).value;
    var link = document.getElementById('link' + id).value;
    var filePath = document.getElementById('filePath' + id).value;

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var data = {
        fileDescription: fileDescription,
        fileType: fileType,
        fileVersion: fileVersion || null, // Handle potential null values
        link: fileType === '2' ? link : null, // Handle link only for fileType === '2'
        filePath: fileType === '1' ? filePath : null // Handle filePath only for fileType === '1'
    };

    // Send an AJAX request to update the task
    $.ajax({
        url: '{{ route('admin.updateresources', '') }}' + '/' + id,
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            // Display success message
            alert('Resource updated successfully');
            // Reload the page to reflect the new data
            location.reload();
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(xhr.responseText);
        }
    });

    // Close the modal
    $('#editTaskModal' + id).modal('hide');

    // Prevent form submission
    return false;
}



</script>
</html>
