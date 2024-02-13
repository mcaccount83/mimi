@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Admin
       <small>Resources</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Resources</li>
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

                    <div class="row">
                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">BYLAWS</h3>
                                    &nbsp;&nbsp;&nbsp;
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Resource</button>
                                </div>
                                <div class="box-body">
                                        @foreach($resources->where('category', 5) as $resourceItem)
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="col-md-9">
                                                        <h4>
                                                            @if ($resourceItem->link)
                                                                <a href="{{ $resourceItem->link }}">{{ $resourceItem->name }}</a>
                                                            @elseif ($resourceItem->file_path)
                                                                <a href="{{ $resourceItem->file_path }}">{{ $resourceItem->name }}</a>
                                                            @else
                                                                {{ $resourceItem->name }}
                                                            @endif
                                                        </h4>
                                                    </div>
                                                    <!-- Button to open modal for editing -->
                                                    <div class="col-md-3 text-center">
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#editResourceModal{{ $resourceItem->id }}">
                                                        <i class="fa fa-info-circle fa-fw" aria-hidden="true" ></i>&nbsp; Update
                                                    </button>
                                                    </div>
                                                    <div class="col-md-12"><br></div>
                                                </div>
                                            </div>

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
                                                                    <label for="fileDetails">Description</label>
                                                                    <textarea class="form-control" id="fileDetails{{ $resourceItem->id }}">{{ $resourceItem->description }}</textarea>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="fileDetails">Version</label>
                                                                    <textarea class="form-control" id="fileDetails{{ $resourceItem->id }}">{{ $resourceItem->version }}</textarea>
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
                                    <h3 class="box-title">CHAPTER RESOURCES</h3>
                                    &nbsp;&nbsp;&nbsp;
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Resource</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">SAMPLE CHAPTER FILES</h3>
                                    &nbsp;&nbsp;&nbsp;
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Resource</button>
                                </div>
                            </div>
                        </div>
                </div>    {{-- END ROW --}}

                <div class="row">
                    <div class="col-md-4">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">FACT SHEETS</h3>
                                &nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Resource</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">IDEAS AND INSPIRATION</h3>
                                &nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Resource</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">END OF YEAR</h3>
                                &nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Resource</button>
                            </div>
                        </div>
                    </div>
                </div>    {{-- END ROW --}}

                       <div class="row">
                            <div class="col-md-4">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">COPY READY MATERIAL</h3>
                                        &nbsp;&nbsp;&nbsp;
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Resource</button>
                                    </div>
                                </div>
                            </div>
                        </div>    {{-- END ROW --}}

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
                            <label for="fileNameNew">Resource </label>
                            <input type="text" class="form-control" id="fileNameNew" name="fileNameNew">
                        </div>
                        <label>Category</label>
                        <select name="fileCategoryNew" class="form-control select2" style="width: 50%;" id="fileCategoryNew" >
                            <option value="1" >Bylaws</option>
                            <option value="2" >Fact Sheets</option>
                            <option value="3" >Copy Ready Materials</option>
                            <option value="4" >Ideas & Inspiration</option>
                            <option value="5" >Chapter Resources</option>
                            <option value="6" >Sample Chapter Files</option>
                            <option value="7" >End of Year</option>
                        </select>
                        <div class="form-group">
                            <label for="fileDetailsNew">Description</label>
                            <textarea class="form-control" id="fileDescriptionNew" name="fileDescriptionNew"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="fileVersionNew">Version</label>
                            <textarea class="form-control" id="fileVersionNew" name="fileVersionNew"></textarea>
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

<script>
 function addFile() {
    var fileNameNew = document.getElementById('taskNameNew').value;
    var fileDescriptionNew = document.getElementById('taskDetailsNew').value;
    var fileVersionNew = document.getElementById('taskPriorityNew').value;

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

    // Send an AJAX request to Laravel backend to create a new task
    $.ajax({
        url: '{{ route('admin.addresources') }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            fileNameNew: fileNameNew,
            fileDescriptionNew: fileDescriptionNew,
            fileVersionNew: fileVersionNew
        },
        success: function(response) {
            // Display success message
            alert('Task added successfully');
            // Reload the page to reflect the new data
            location.reload();
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
    var taskDetails = document.getElementById('taskDetails' + id).value;
    var taskNotes = document.getElementById('taskNotes' + id).value;
    var taskStatus = document.getElementById('taskStatus' + id).value;
    var taskPriority = document.getElementById('taskPriority' + id).value;

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send an AJAX request to update the task
    $.ajax({
        url: '{{ route('admin.updateprogression', '') }}' + '/' + id,
        method: 'POST',
        data: {
            taskDetails: taskDetails,
            taskNotes: taskNotes,
            taskStatus: taskStatus,
            taskPriority: taskPriority
        },
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            // Display success message
            alert('Task updated successfully');
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
