@extends('layouts.coordinator_theme')

@section('content')
 <section class="content-header">
      <h1>
        Admin
       <small>Progression</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('coordinator.showdashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="active">Chapter List</li>
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
              <h3 class="box-title">MIMI Bugs & Wishes</h3>
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
                                    <h3 class="box-title">To Do</h3>
                                    &nbsp;&nbsp;&nbsp;
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-task"><i class="fa fa-plus fa-fw" aria-hidden="true" ></i>&nbsp; Add Task</button>
                                </div>
                                <div class="box-body">
                                    @if($admin->where('status', 1)->isEmpty())
                                        <p>No jobs with this status</p>
                                    @else
                                        @foreach($admin->where('status', 1) as $adminItem)
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="col-md-7">
                                                    <h4>{{ $adminItem->task }}</h4>
                                                    </div>
                                                    <div class="col-md-2">
                                                    <h5 style="background-color:
                                                        @if ($adminItem->priority == 1)
                                                            #C6EFCE     /*GREEN*/
                                                        @elseif ($adminItem->priority == 2)
                                                            #FFEB9C     /*YELLOW*/
                                                        @elseif ($adminItem->priority == 3)
                                                            #FFC7CE  /*RED*/
                                                        @else
                                                            #FFFFFF  /*WHITE*/
                                                        @endif
                                                    ">
                                                        <center>{{ $adminItem->priority_word }}<center></h5>
                                                    </div>
                                                    <!-- Button to open modal for editing -->
                                                    <div class="col-md-3 text-center">
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#editTaskModal{{ $adminItem->id }}">
                                                        <i class="fa fa-info-circle fa-fw" aria-hidden="true" ></i>&nbsp; Details
                                                    </button>
                                                    </div>
                                                    <div class="col-md-12"><br></div>
                                                </div>
                                            </div>

                                            <!-- Modal for editing task -->
                                            <div class="modal fade" id="editTaskModal{{ $adminItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editTaskModal{{ $adminItem->id }}Label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span></button>
                                                                <h3 class="modal-title" id="editTaskModal{{ $adminItem->id }}Label">{{ $adminItem->task }}</h3>
                                                            </div>
                                                        <div class="modal-body">
                                                            <form>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="taskDetails">Description</label>
                                                                    <textarea class="form-control" id="taskDetails{{ $adminItem->id }}">{{ $adminItem->details }}</textarea>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="taskNotes">IT Dept Notes</label>
                                                                    <textarea class="form-control" id="taskNotes{{ $adminItem->id }}" rows="5" {{ $canEditDetails ? '' : 'disabled' }}>{{ $adminItem->notes }}</textarea>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                <label>Status</label>
                                                                <select name="taskStatus" class="form-control select2" style="width: 100%;" id="taskStatus{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->status == 1 ? 'selected' : ''}}>ToDo</option>
                                                                    <option value="2" {{$adminItem->status == 2 ? 'selected' : ''}}>In Progress</option>
                                                                    <option value="3" {{$adminItem->status == 3 ? 'selected' : ''}}>Done</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                <label>Priority</label>
                                                                <select name="taskPriority" class="form-control select2" style="width: 100%;" id="taskPriority{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->priority == 1 ? 'selected' : ''}}>Low</option>
                                                                    <option value="2" {{$adminItem->priority == 2 ? 'selected' : ''}}>Normal</option>
                                                                    <option value="3" {{$adminItem->priority == 3 ? 'selected' : ''}}>High</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6"><br></div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    Reported by <strong>{{ $adminItem->reported_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong>
                                                                </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6"><br></div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true" ></i>&nbsp; Close</button>
                                                            <button type="button" class="btn btn-success" onclick="updateTask({{ $adminItem->id }})"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save changes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h3 class="box-title">In Production</h3>
                                </div>
                                <div class="box-body">
                                    @if($admin->where('status', 2)->isEmpty())
                                        <p>No jobs with this status</p>
                                    @else
                                        @foreach($admin->where('status', 2) as $adminItem)
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="col-md-7">
                                                    <h4>{{ $adminItem->task }}</h4>
                                                    </div>
                                                    <div class="col-md-2">
                                                    <h5 style="background-color:
                                                        @if ($adminItem->priority == 1)
                                                            #C6EFCE     /*GREEN*/
                                                        @elseif ($adminItem->priority == 2)
                                                            #FFEB9C     /*YELLOW*/
                                                        @elseif ($adminItem->priority == 3)
                                                            #FFC7CE  /*RED*/
                                                        @else
                                                            #FFFFFF  /*WHITE*/
                                                        @endif
                                                    ">
                                                        <center>{{ $adminItem->priority_word }}<center></h5>
                                                    </div>
                                                    <!-- Button to open modal for editing -->
                                                    <div class="col-md-3 text-center">
                                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#editTaskModal{{ $adminItem->id }}">
                                                        <i class="fa fa-info-circle fa-fw" aria-hidden="true" ></i>&nbsp; Details
                                                    </button>
                                                    </div>
                                                    <div class="col-md-12"><br></div>
                                                </div>
                                            </div>

                                            <!-- Modal for editing task -->
                                            <div class="modal fade" id="editTaskModal{{ $adminItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editTaskModal{{ $adminItem->id }}Label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span></button>
                                                                <h3 class="modal-title" id="editTaskModal{{ $adminItem->id }}Label">{{ $adminItem->task }}</h3>
                                                            </div>
                                                        <div class="modal-body">
                                                            <form>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="taskDetails">Description</label>
                                                                    <textarea class="form-control" id="taskDetails{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>{{ $adminItem->details }}</textarea>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="taskNotes">IT Dept Notes</label>
                                                                    <textarea class="form-control" id="taskNotes{{ $adminItem->id }}" rows="5" {{ $canEditDetails ? '' : 'disabled' }}>{{ $adminItem->notes }}</textarea>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                <label>Status</label>
                                                                <select name="taskStatus" class="form-control select2" style="width: 100%;" id="taskStatus{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->status == 1 ? 'selected' : ''}}>ToDo</option>
                                                                    <option value="2" {{$adminItem->status == 2 ? 'selected' : ''}}>In Progress</option>
                                                                    <option value="3" {{$adminItem->status == 3 ? 'selected' : ''}}>Done</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                <label>Priority</label>
                                                                <select name="taskPriority" class="form-control select2" style="width: 100%;" id="taskPriority{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->priority == 1 ? 'selected' : ''}}>Low</option>
                                                                    <option value="2" {{$adminItem->priority == 2 ? 'selected' : ''}}>Normal</option>
                                                                    <option value="3" {{$adminItem->priority == 3 ? 'selected' : ''}}>High</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6"><br></div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    Reported by <strong>{{ $adminItem->reported_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong>
                                                                </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6"><br></div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true" ></i>&nbsp; Close</button>
                                                            <button type="button" class="btn btn-success" onclick="updateTask({{ $adminItem->id }})"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save changes</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                            <div class="col-md-4">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Done</h3>
                                    </div>
                                    <div class="box-body">
                                        @if($admin->where('status', 3)->isEmpty())
                                            <p>No jobs with this status</p>
                                        @else
                                            @foreach($admin->where('status', 3) as $adminItem)
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="col-md-7">
                                                        <h4>{{ $adminItem->task }}</h4>
                                                        </div>
                                                        <div class="col-md-2">
                                                        <h5 style="background-color:
                                                            @if ($adminItem->priority == 1)
                                                                #C6EFCE     /*GREEN*/
                                                            @elseif ($adminItem->priority == 2)
                                                                #FFEB9C     /*YELLOW*/
                                                            @elseif ($adminItem->priority == 3)
                                                                #FFC7CE  /*RED*/
                                                            @else
                                                                #FFFFFF  /*WHITE*/
                                                            @endif
                                                        ">
                                                            <center>{{ $adminItem->priority_word }}<center></h5>
                                                        </div>

                                                        <!-- Button to open modal for editing -->
                                                        <div class="col-md-3 text-center">
                                                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#editTaskModal{{ $adminItem->id }}">
                                                            <i class="fa fa-info-circle fa-fw" aria-hidden="true" ></i>&nbsp; Details
                                                        </button>
                                                        </div>
                                                        <div class="col-md-12"><br></div>
                                                    </div>
                                                </div>

                                                <!-- Modal for editing task -->
                                                <div class="modal fade" id="editTaskModal{{ $adminItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editTaskModal{{ $adminItem->id }}Label" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span></button>
                                                                    <h3 class="modal-title" id="editTaskModal{{ $adminItem->id }}Label">{{ $adminItem->task }}</h3>
                                                                </div>
                                                            <div class="modal-body">
                                                                <form>
                                                                    <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="taskDetails">Description</label>
                                                                        <textarea class="form-control" id="taskDetails{{ $adminItem->id }}" disabled>{{ $adminItem->details }}</textarea>
                                                                    </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="taskNotes">IT Dept Notes</label>
                                                                        <textarea class="form-control" id="taskNotes{{ $adminItem->id }}" rows="5" disabled>{{ $adminItem->notes }}</textarea>
                                                                    </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                    <label>Status</label>
                                                                    <select name="taskStatus" class="form-control select2" style="width: 100%;" id="taskStatus{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                        <option value="1" {{$adminItem->status == 1 ? 'selected' : ''}}>ToDo</option>
                                                                        <option value="2" {{$adminItem->status == 2 ? 'selected' : ''}}>In Progress</option>
                                                                        <option value="3" {{$adminItem->status == 3 ? 'selected' : ''}}>Done</option>
                                                                    </select>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                    <label>Priority</label>
                                                                    <select name="taskPriority" class="form-control select2" style="width: 100%;" id="taskPriority{{ $adminItem->id }}" disabled>
                                                                        <option value="1" {{$adminItem->priority == 1 ? 'selected' : ''}}>Low</option>
                                                                        <option value="2" {{$adminItem->priority == 2 ? 'selected' : ''}}>Normal</option>
                                                                        <option value="3" {{$adminItem->priority == 3 ? 'selected' : ''}}>High</option>
                                                                    </select>
                                                                    </div>
                                                                    <div class="col-md-6"><br></div>
                                                                    <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        Reported by <strong>{{ $adminItem->reported_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong>
                                                                    </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        Completed on <strong>{{ \Carbon\Carbon::parse($adminItem->completed_date)->format('m-d-Y') }}</strong>
                                                                    </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="col-md-6"><br></div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true" ></i>&nbsp; Close</button>
                                                                <button type="button" class="btn btn-success" onclick="updateTask({{ $adminItem->id }})"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Save changes</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
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
                    <h4 class="modal-title">Add a New Task</h4>
                </div>
                <div class="modal-body">
                    <form id="addTaskForm">
                        <div class="form-group">
                            <label for="taskNameNew">Task </label>
                            <input type="text" class="form-control" id="taskNameNew" name="taskNameNew">
                        </div>
                        <div class="form-group">
                            <label for="taskDetailsNew">Description</label>
                            <textarea class="form-control" id="taskDetailsNew" name="taskDetailsNew"></textarea>
                        </div>
                        <label>Priority</label>
                            <select name="taskPriorityNew" class="form-control select2" style="width: 50%;" id="taskPriorityNew" >
                                <option value="1" >Low</option>
                                <option value="2" >Normal</option>
                                <option value="3" >High</option>
                            </select>
                        </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true" ></i>&nbsp; Close</button>
                    <button type="button" class="btn btn-success" onclick="return addTask()"><i class="fa fa-floppy-o fa-fw" aria-hidden="true" ></i>&nbsp; Add Task</button>
                </div>
            </div>
        </div>
    </div>

        </section>
        <!-- /.content -->

    @endsection

<script>
 function addTask() {
    var taskNameNew = document.getElementById('taskNameNew').value;
    var taskDetailsNew = document.getElementById('taskDetailsNew').value;
    var taskPriorityNew = document.getElementById('taskPriorityNew').value;

    if (taskNameNew == '') {
        alert('Task Name is Required.');
        return false; // Prevent form submission
    }
    if (taskNameNew.length > 50) {
        alert('Name cannot exceed 50 characters.');
        return false; // Prevent form submission
    }
    if (taskDetailsNew == '') {
        alert('Task Description is Required.');
        return false; // Prevent form submission
    }
    if (taskDetailsNew.length > 255) {
        alert('Description cannot exceed 255 characters.');
        return false; // Prevent form submission
    }

    // Get the CSRF token value from the meta tag
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Send an AJAX request to Laravel backend to create a new task
    $.ajax({
        url: '{{ route('admin.addprogression') }}',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            taskNameNew: taskNameNew,
            taskDetailsNew: taskDetailsNew,
            taskPriorityNew: taskPriorityNew
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

  function updateTask(id) {
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

