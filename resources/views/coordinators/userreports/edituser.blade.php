@extends('layouts.mimi_theme')

@section('page_title', 'User Details')
@section('breadcrumb', 'Edit User')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>

@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("userreports.updateuser", $userDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">

            <div class="card card-primary card-outline">
              <div class="card-body">

                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">

                        <div class="row mb-3">
                            <div class="col-auto fw-bold">Missing From:</div>
                            <div class="col text-end">
                                {{ $userDetails->missing_from ?? 'None' }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-auto fw-bold">Found In:</div>
                            <div class="col text-end">
                                {{ !empty($userDetails->wrong_tables) ? implode(', ', $userDetails->wrong_tables) : 'None' }}
                            </div>
                        </div>

                     <div class="row mb-3">
                        <div class="col-auto fw-bold">Action:</div>
                        <div class="col text-end">
                            @if($userDetails->missing_from !== null && empty($userDetails->wrong_tables))
                                <span class="badge bg-danger fs-7">Make user inactive</span>
                            @elseif(!empty($userDetails->wrong_tables))
                                @foreach($userDetails->wrong_tables as $table)
                                    <span class="badge bg-warning text-dark fs-7">Change user type to match {{ $table }}</span>
                                @endforeach
                            @else
                                <span class="badge bg-success fs-7">No action needed</span>
                            @endif
                        </div>
                    </div>

                        <div class="row mb-1">
                            <div class="col-auto fw-bold">Active Status:</div>
                            <div class="col text-end">
                                <select id="status" name="status" class="form-control float-end text-end"required>
                                    @foreach($AllUserStatus as $status)
                                        <option value="{{$status->id}}"
                                            @if($userDetails->is_active == $status->id) selected @endif>
                                            {{$status->user_status}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-auto fw-bold">User Type:</div>
                            <div class="col text-end">
                                <select id="type" name="type" class="form-control float-end text-end"required>
                                    @foreach($AllUserType as $type)
                                        <option value="{{$type->id}}"
                                            @if($userDetails->type_id == $type->id) selected @endif>
                                            {{$type->user_type}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-auto fw-bold">Admin Role:</div>
                            <div class="col text-end">
                                <select id="role" name="role" class="form-control float-end text-end"required>
                                    @foreach($AllAdminRole as $role)
                                        <option value="{{$role->id}}"
                                            @if($userDetails->is_admin == $role->id) selected @endif>
                                            {{$role->admin_role}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </li>
               </ul>

              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                <h3>User Information</h3>
                      </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                         <!-- /.form group -->
                        <div class="row mb-3">

                            <label class="col-sm-2 mb-3 col-form-label">Name:</label>
                            <div class="col-sm-5 mb-3">
                                <input type="text" name="fname" id="fname" class="form-control" required placeholder="First Name" value="{{ $userDetails->first_name }}">
                            </div>
                            <div class="col-sm-5 mb-3">
                                <input type="text" name="lname" id="lname" class="form-control" required placeholder="Last Name" value="{{ $userDetails->last_name }}">
                            </div>
                            <label class="col-sm-2 mb-3 col-form-label">Email:</label>
                            <div class="col-sm-5 mb-3">
                        <input type="text" name="email" id="email" class="form-control" required placeholder="Email Address" value="{{ $userDetails->email }}">

                        </div>

                        </div>
                        </div>
                    </div>

                  </div>
                    </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->
          <div class="col-md-12">
            <div class="card-body text-center mt-3">
                <button type="submit" class="btn btn-primary bg-gradient mb-2" onclick="return validateEmailsBeforeSubmit();"><i class="bi bi-floppy-fill me-2"></i>Save User Information</button>
                {{-- @if($userDetails->type_id == \App\Enums\UserTypeEnum::COORD)
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('userreports.usernoactivecoord') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-gear me-2"></i>Back to Coordinator User List</button>
                @elseif($userDetails->type_id == \App\Enums\UserTypeEnum::BOARD) --}}
                    <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="window.location.href='{{ route('userreports.userdetailsmismatch') }}'"><i class="bi bi-arrow-left-short"></i><i class="bi bi-person-fill-gear me-2"></i>Back to User List</button>
                {{-- @endif --}}
            </div>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
<script>

</script>
@endsection
