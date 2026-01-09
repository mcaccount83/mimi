@extends('layouts.coordinator_theme')

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

        <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">


                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">

                          <div class="form-group row mt-1">
                            <label class="col-form-label col-sm-6">Active Status:</label>
                            <div class="col-sm-6">
                                <select id="status" name="status" class="form-control float-right text-right"required>
                                    @foreach($AllUserStatus as $status)
                                        <option value="{{$status->id}}"
                                            @if($userDetails->is_active == $status->id) selected @endif>
                                            {{$status->user_status}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                           <div class="form-group row mt-1">
                            <label class="col-form-label col-sm-6">User Type:</label>
                            <div class="col-sm-6">
                                <select id="type" name="type" class="form-control float-right text-right"required>
                                    @foreach($AllUserType as $type)
                                        <option value="{{$type->id}}"
                                            @if($userDetails->type_id == $type->id) selected @endif>
                                            {{$type->user_type}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                           <div class="form-group row mt-1">
                            <label class="col-form-label col-sm-6">Admin Role:</label>
                            <div class="col-sm-6">
                                <select id="role" name="role" class="form-control float-right text-right"required>
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
                <div class="card-body box-profile">
                <h3 class="profile-username">User Information</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">
                         <!-- /.form group -->
                        <div class="form-group row">

                            <label class="col-sm-2 mb-3 col-form-label">Name:</label>
                            <div class="col-sm-5 mb-3">
                                <input type="text" name="fname" id="fname" class="form-control" required placeholder="First Name" value="{{ $userDetails->first_name }}">
                            </div>
                            <div class="col-sm-5 mb-3">
                                <input type="text" name="lname" id="lname" class="form-control" required placeholder="Last Name" value="{{ $userDetails->last_name }}">
                            </div>
                            <label class="col-sm-2 mb-3 col-form-label">Email:</label>
                            <div class="col-sm-5 mb-3">
                        <input type="text" name="email" id="email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)" required placeholder="Email Address" value="{{ $userDetails->email }}">

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
            <div class="card-body text-center">
                <button type="submit" class="btn bg-gradient-primary mb-3" onclick="return validateEmailsBeforeSubmit();"><i class="fas fa-save mr-2"></i>Save User Information</button>
                @if($userDetails->type_id == \App\Enums\UserTypeEnum::COORD)
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('userreports.usernoactivecoord') }}'"><i class="fas fa-reply mr-2"></i>Back to List</button>
                @elseif($userDetails->type_id == \App\Enums\UserTypeEnum::BOARD)
                    <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ route('userreports.usernoactiveboard') }}'"><i class="fas fa-reply mr-2"></i>Back to List</button>
                @endif
                {{-- <button type="button" class="btn bg-gradient-primary mb-3" onclick="window.location.href='{{ request('return') }}'">
    <i class="fas fa-reply mr-2"></i>Back to List
</button> --}}
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
