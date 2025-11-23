@extends('layouts.coordinator_theme')

@section('page_title', 'Resources')
@section('breadcrumb', 'MIMI Bugs & Wishes')

@section('content')
  <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="col-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                      <div class="dropdown">
                          <h3 class="card-title dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            MIMI Bugs & Wishes
                          </h3>
                            @include('layouts.dropdown_menus.menu_reports_admin')
                      </div>
                  </div>
                  <!-- /.card-header -->
              <div class="card-body">
            <div class="row">
                &nbsp;&nbsp;<button type="button" class="btn bg-gradient-success" data-toggle="modal" data-target="#modal-task"><i class="fas fa-plus" ></i>&nbsp;&nbsp;&nbsp;Add Task</button>
            </div>
            <div class="row">&nbsp;</div>
            <div class="row">

                <div class="col-md-4">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">To Do</h3>
                        </div>
                                <div class="card-body">
                                    @if($admin->where('status', 1)->isEmpty())
                                        <p>No jobs with this status</p>
                                    @else
                                        @foreach($admin->where('status', 1) as $adminItem)
                                        <div class="card card-outline card-secondary">
                                            <div class="card-header">
                                              <h5 class="card-title">{{ $adminItem->task }}</h5>
                                              <div class="card-tools">
                                                <h5
                                                    @if ($adminItem->priority == 1)
                                                        style="background-color:#28a745; color: #ffffff;"
                                                    @elseif ($adminItem->priority == 2)
                                                        style="background-color:#ffc107;"
                                                    @elseif ($adminItem->priority == 3)
                                                        style="background-color:#dc3545; color: #ffffff;"
                                                    @else
                                                        style="background-color:#FFFFFF;"
                                                    @endif
                                                        >{{ $adminItem->priority_word }}</h5>
                                              </div>
                                            </div>
                                            <div class="card-body">
                                              <p>
                                                {{ $adminItem->details }}
                                                <br><br>
                                                <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#editTaskModal{{ $adminItem->id }}">
                                                    <i class="fas fa-info-circle"></i>&nbsp;View/Edit Details</button>
                                              </p>
                                            </div>
                                          </div>
                                            <!-- Modal for editing task -->
                                            <div class="modal fade" id="editTaskModal{{ $adminItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editTaskModal{{ $adminItem->id }}Label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
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
                                                                <select name="taskStatus" class="form-control select2-bs4" style="width: 100%;" id="taskStatus{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->status == 1 ? 'selected' : ''}}>ToDo</option>
                                                                    <option value="2" {{$adminItem->status == 2 ? 'selected' : ''}}>In Progress</option>
                                                                    <option value="3" {{$adminItem->status == 3 ? 'selected' : ''}}>Done</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                <label>Priority</label>
                                                                <select name="taskPriority" class="form-control select2-bs4" style="width: 100%;" id="taskPriority{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->priority == 1 ? 'selected' : ''}}>Low</option>
                                                                    <option value="2" {{$adminItem->priority == 2 ? 'selected' : ''}}>Normal</option>
                                                                    <option value="3" {{$adminItem->priority == 3 ? 'selected' : ''}}>High</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6"><br></div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    {{-- Reported by <strong>{{ $adminItem->reported_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong> --}}
                                                                    Reported by <strong>{{ $adminItem->updatedBy->first_name }} {{ $adminItem->updatedBy->last_name }}</strong> on <strong>{{ \Illuminate\Support\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong>
                                                                </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6"><br></div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp; Close</button>
                                                            <button type="button" class="btn btn-success" onclick="updateTask({{ $adminItem->id }})"><i class="fas fa-save" ></i>&nbsp; Save changes</button>
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
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">In Production</h3>
                                </div>
                                    <div class="card-body">
                                        @if($admin->where('status', 2)->isEmpty())
                                            <p>No jobs with this status</p>
                                        @else
                                            @foreach($admin->where('status', 2) as $adminItem)
                                            <div class="card card-outline card-primary">
                                                <div class="card-header">
                                                <h5 class="card-title">{{ $adminItem->task }}</h5>
                                                <div class="card-tools">
                                                    <h5
                                                        @if ($adminItem->priority == 1)
                                                            style="background-color:#28a745; color: #ffffff;"
                                                        @elseif ($adminItem->priority == 2)
                                                            style="background-color:#ffc107;"
                                                        @elseif ($adminItem->priority == 3)
                                                            style="background-color:#dc3545; color: #ffffff;"
                                                        @else
                                                            style="background-color:#FFFFFF;"
                                                        @endif
                                                            >{{ $adminItem->priority_word }}</h5>
                                                </div>
                                                </div>
                                                <div class="card-body">
                                                <p>
                                                    {{ $adminItem->details }}
                                                    <br><br>
                                                    <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#editTaskModal{{ $adminItem->id }}">
                                                        <i class="fas fa-info-circle"></i>&nbsp;View/Edit Details</button>
                                                </p>
                                                </div>
                                            </div>
                                            <!-- Modal for editing task -->
                                            <div class="modal fade" id="editTaskModal{{ $adminItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editTaskModal{{ $adminItem->id }}Label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
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
                                                                <select name="taskStatus" class="form-control select2-bs4" style="width: 100%;" id="taskStatus{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->status == 1 ? 'selected' : ''}}>ToDo</option>
                                                                    <option value="2" {{$adminItem->status == 2 ? 'selected' : ''}}>In Progress</option>
                                                                    <option value="3" {{$adminItem->status == 3 ? 'selected' : ''}}>Done</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                <label>Priority</label>
                                                                <select name="taskPriority" class="form-control select2-bs4" style="width: 100%;" id="taskPriority{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->priority == 1 ? 'selected' : ''}}>Low</option>
                                                                    <option value="2" {{$adminItem->priority == 2 ? 'selected' : ''}}>Normal</option>
                                                                    <option value="3" {{$adminItem->priority == 3 ? 'selected' : ''}}>High</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6"><br></div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    {{-- Reported by <strong>{{ $adminItem->reported_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong> --}}
                                                                    Reported by <strong>{{ $adminItem->updatedBy->first_name }} {{ $adminItem->updatedBy->last_name }}</strong> on <strong>{{ \Illuminate\Support\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong>
                                                                </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6"><br></div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp; Close</button>
                                                            <button type="button" class="btn btn-success" onclick="updateTask({{ $adminItem->id }})"><i class="fas fa-save" ></i>&nbsp; Save changes</button>
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
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Done</h3>
                                </div>
                                    <div class="card-body">
                                        @php
                                        // Filter and sort the collection
                                        $doneItems = $admin->where('status', 3)->sortByDesc('completed_date');
                                    @endphp

                                    @if($doneItems->isEmpty())
                                        <p>No jobs with this status</p>
                                    @else
                                        @foreach($doneItems as $adminItem)
                                            <div class="card card-outline card-success">
                                                <div class="card-header">
                                                <h5 class="card-title">{{ $adminItem->task }}</h5>
                                                <div class="card-tools">
                                                    <h5
                                                        @if ($adminItem->priority == 1)
                                                            style="background-color:#28a745; color: #ffffff;"
                                                        @elseif ($adminItem->priority == 2)
                                                            style="background-color:#ffc107;"
                                                        @elseif ($adminItem->priority == 3)
                                                            style="background-color:#dc3545; color: #ffffff;"
                                                        @else
                                                            style="background-color:#FFFFFF;"
                                                        @endif
                                                            >{{ $adminItem->priority_word }}</h5>
                                                </div>
                                                </div>
                                                <div class="card-body">
                                                <p>
                                                    {{ $adminItem->details }}
                                                    <br><br>
                                                    <button type="button" class="btn bg-gradient-primary" data-toggle="modal" data-target="#editTaskModal{{ $adminItem->id }}">
                                                        <i class="fas fa-info-circle"></i>&nbsp;View/Edit Details</button>
                                                </p>
                                                </div>
                                            </div>

                                            <!-- Modal for editing task -->
                                            <div class="modal fade" id="editTaskModal{{ $adminItem->id }}" tabindex="-1" role="dialog" aria-labelledby="editTaskModal{{ $adminItem->id }}Label" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
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
                                                                <select name="taskStatus" class="form-control select2-bs4" style="width: 100%;" id="taskStatus{{ $adminItem->id }}" {{ $canEditDetails ? '' : 'disabled' }}>
                                                                    <option value="1" {{$adminItem->status == 1 ? 'selected' : ''}}>ToDo</option>
                                                                    <option value="2" {{$adminItem->status == 2 ? 'selected' : ''}}>In Progress</option>
                                                                    <option value="3" {{$adminItem->status == 3 ? 'selected' : ''}}>Done</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                <label>Priority</label>
                                                                <select name="taskPriority" class="form-control select2-bs4" style="width: 100%;" id="taskPriority{{ $adminItem->id }}" disabled>
                                                                    <option value="1" {{$adminItem->priority == 1 ? 'selected' : ''}}>Low</option>
                                                                    <option value="2" {{$adminItem->priority == 2 ? 'selected' : ''}}>Normal</option>
                                                                    <option value="3" {{$adminItem->priority == 3 ? 'selected' : ''}}>High</option>
                                                                </select>
                                                                </div>
                                                                <div class="col-md-6"><br></div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    {{-- Reported by <strong>{{ $adminItem->reported_by }}</strong> on <strong>{{ \Carbon\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong> --}}
                                                                    Reported by <strong>{{ $adminItem->updatedBy->first_name }} {{ $adminItem->updatedBy->last_name }}</strong> on <strong>{{ \Illuminate\Support\Carbon::parse($adminItem->reported_date)->format('m-d-Y') }}</strong>
                                                                </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                <div class="form-group">
                                                                    {{-- Completed on <strong>{{ \Carbon\Carbon::parse($adminItem->completed_date)->format('m-d-Y') }}</strong> --}}
                                                                    Completed on <strong>{{ \Illuminate\Support\Carbon::parse($adminItem->completed_date)->format('m-d-Y') }}</strong>
                                                                </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="col-md-6"><br></div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp; Close</button>
                                                            <button type="button" class="btn btn-success" onclick="updateBugTask({{ $adminItem->id }})"><i class="fas fa-save" ></i>&nbsp; Save changes</button>
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

        </div>
    </div>
    </div>

 <div class="modal fade" id="modal-task">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
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
                            <select name="taskPriorityNew" class="form-control select2-bs4" style="width: 50%;" id="taskPriorityNew" >
                                <option value="1" >Low</option>
                                <option value="2" >Normal</option>
                                <option value="3" >High</option>
                            </select>
                        </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp; Close</button>
                    <button type="button" class="btn btn-success" onclick="return addBugTask()"><i class="fas fa-save" ></i>&nbsp; Add Task</button>
                </div>
            </div>
        </div>
    </div>

        </section>
    <!-- /.content -->

@endsection

