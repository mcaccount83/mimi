@extends('layouts.coordinator_theme')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Coordinator Details&nbsp;<small>(Add)</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinators.coorddashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Coordinator Details</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

    <!-- Main content -->
    <form method="POST" id="create_form" action="{{ route('coordinators.updatecoordnew') }}" autocomplete="off">
    @csrf
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Personal Information</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
              <!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>First Name</label><span class="field-required">*</span>
						<input type="text" name="cord_fname" id="cord_fname" class="form-control"  required autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Last Name</label><span class="field-required">*</span>
						<input type="text" name="cord_lname" id="cord_lname" class="form-control"  required autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-12">
						<div class="form-group">
							<label>Street Address</label><span class="field-required">*</span>
							<input autocomplete="nope" name="cord_addr" id="cord_addr" class="form-control" rows="4"  required >
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3">
						<div class="form-group">
							<label>City</label><span class="field-required">*</span>
							<input type="text" name="cord_city" id="cord_city" class="form-control"  required autocomplete="nope">
						</div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-3">
					  <div class="form-group">
						<label>State</label><span class="field-required">*</span>
						<select name="cord_state" id="cord_state" class="form-control select2-sb4" style="width: 100%;" required>
						<option value="">Select State</option>
							@foreach($stateArr as $state)
							  <option value="{{$state->state_short_name}}">{{$state->state_long_name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<!-- /.form group -->

					<div class="col-sm-3">
					  <div class="form-group">
						<label>Country</label><span class="field-required">*</span>
						<?php $selectedvalue = 'USA' ?>
						<select id="cord_country" name="cord_country" id="cord_country" class="form-control select2-sb4" style="width: 100%;" required>
						<option value="">Select Country</option>
							@foreach($countryArr as $con)
							  <option value="{{$con->short_name}}" {{ $selectedvalue == $con->short_name ? 'selected="selected"' : '' }}>{{$con->name}}</option>
							@endforeach
						</select>
					  </div>
					</div>
					<div class="col-sm-3">
					  <div class="form-group">
						<label>Zip</label><span class="field-required">*</span>
						<input type="text" name="cord_zip" id="cord_zip" class="form-control"  required autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Email</label><span class="field-required">*</span>
						<input type="email" name="cord_email" id="cord_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  required autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Secondary Email</label>
						<input type="sec_email" name="sec_email" id="sec_email" class="form-control"  autocomplete="nope">
					  </div>
					</div>
					<!-- /.form group -->
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Phone</label><span class="field-required">*</span>
						<input type="tel" name="cord_phone" id="cord_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask required autocomplete="nope">
					  </div>
					</div>
					<div class="col-sm-6">
					  <div class="form-group">
						<label>Alternate Phone</label>
						<input type="tel" name="cord_altphone" id="cord_altphone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask autocomplete="nope">
					  </div>
					</div>
                    <div class="col-sm-6">
                        <div class="form-group">
                          <label>Home Chapter</label><span class="field-required">*</span>
                          <input type="text" name="cord_chapter" id="cord_chapter" class="form-control"  required  autocomplete="nope">
                        </div>
                      </div>
					<div class="col-sm-3">
						<div class="form-group">
						<label>Birthday Month</label><span class="field-required">*</span>
						<select name="cord_month" id="cord_month" class="form-control select2-sb4" style="width: 100%;" required>
						  <option value="">Select Month</option>
						  @foreach($foundedMonth as $key=>$val)
							  <option value="{{$key}}">{{$val}}</option>
						  @endforeach
						</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
						<label>Birthday Day</label><span class="field-required">*</span>
						<input type="number" name="cord_day" id="cord_day" class="form-control" min="1" max="31" required>
						</div>
					</div>

				</div>
            </div>

	<!-- /.box-body -->
    <div class="card-body text-center">
			<button type="submit" id="btn-save" class="btn bg-gradient-primary"><i class="fas fa-user-plus" ></i>&nbsp; Create</button>
			<button type="button" class="btn bg-gradient-primary" onclick="ConfirmCancel(this);"><i class="fas fa-undo" ></i>&nbsp; Clear Form</button>
			<a href="{{ route('coordinators.coordlist') }}" class="btn bg-gradient-primary"><i class="fas fa-reply" ></i>&nbsp; Back</a>
		</div>
        <!-- /.box-body -->
        </div>
    </div>

    </section>
</form>

@endsection
@section('customscript')
<script>
function ConfirmCancel(element) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Any unsaved changes will be lost. Do you want to continue?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, continue',
            cancelButtonText: 'No, stay here',
            customClass: {
                confirmButton: 'btn-sm btn-success',
                cancelButton: 'btn-sm btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Reload the page
                location.reload();
            } else {
                // Do nothing if the user cancels
                return false;
            }
        });
    }

    function checkDuplicateEmail(email, id) {
        $.ajax({
            url: '{{ url("/checkemail/") }}' + '/' + email,
            type: "GET",
            success: function(result) {
                if (result.exists) {
                    alert('This Email already used in the system. Please try with new one.');
                    $("#" + id).val('');
                    $("#" + id).focus();
                }
            },
            error: function(jqXHR, exception) {
                console.error("Error checking email: ", exception);
            }
        });
    }

</script>
@endsection

