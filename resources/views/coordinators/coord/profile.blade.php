@extends('layouts.mimi_theme')

@section('page_title', 'Coordinator Profile')
@section('breadcrumb', 'Coordinator Profile')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("coordinators.profileupdate",$cdDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">

        <!-- Start Left Column -->
          <div class="col-md-4">
            <div class="card card-primary card-outline">
              <div class="card-body">
                        <div class="card-header text-center bg-transparent">
                            <h3 class="mb-0">{{ $cdDetails->first_name }}, {{ $cdDetails->last_name }}</h3>
                        <p class="mb-0">{{ $conferenceDescription }} Conference
                            @if ($regionLongName != "None")
                                , {{ $regionLongName }} Region
                            @endif
                        </p>
                    </div>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item mt-2">
                        <b>Supervising Coordinator:</b> <span class="float-end"><a href="mailto:{{ $cdDetails->reportsTo?->email }}">{{ $ReportTo }} </a></span>
                        <br>
                        <b>Primary Position:</b> <span class="float-end">{{ $displayPosition->long_title }}</span>
                        <br>
                        <div style="display: flex; justify-content: space-between;">
                            <b>Secondary Positions:</b>
                            <span style="text-align: right;">
                                @forelse($cdDetails->secondaryPosition as $position)
                                    {{ $position->long_title }}@if(!$loop->last)<br>@endif
                                @empty
                                    None
                                @endforelse
                            </span>
                        </div>

                    </li>
                    <li class="list-group-item mt-2">
                        <b>Start Date:</b> <span class="float-end date-mask">{{ $cdDetails->coordinator_start_date }}</span>
                        <br>
                        <b>Last Promotion Date:</b> <span class="float-end date-mask">{{ $cdDetails->last_promoted }}</span>
                        <br>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label">Home Chapter:</label>
                            <div class="col-sm-6">
                                <input type="text" name="cord_chapter" id="cord_chapter" class="form-control" value="{{ $cdDetails->home_chapter }}" required placeholder="Home Chapter">
                            </div>
                        </div>
                        <br>
                    </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /End Left Column -->

          <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                            <h3>Contact Information</h3>
                    </div>
                    <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Name:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_fname" id="cord_fname" class="form-control" value="{{ $cdDetails->first_name }}"  required >
                                    </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_lname" id="cord_lname" class="form-control" value="{{ $cdDetails->last_name }}"  required >
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Email/Secondary:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_email" id="cord_email" class="form-control" onblur="checkDuplicateEmail(this.value,this.id)"  value="{{ $cdDetails->email }}"  required >
                                    <input type="hidden" id="cord_email_chk" value="{{ $cdDetails->email }}">
                                </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_sec_email"class="form-control" value="{{ $cdDetails->sec_email }}" placeholder="Secondary Email">
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Phone/Alternate:</label>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_phone" id="cord_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $cdDetails->phone }}"  required >
                                    </div>
                                    <div class="col-sm-5">
                                    <input type="text" name="cord_altphone" id="cord_altphone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $cdDetails->alt_phone }}"  placeholder="Alternate Phone" >
                                    </div>
                                </div>
                                <!-- /.form group -->
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Address:</label>
                                    <div class="col-sm-10">
                                    <input type="text" name="cord_addr" id="cord_addr" class="form-control" value="{{ $cdDetails->address }}"  required >
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label"><br></label>
                                    <div class="col-sm-3">
                                    <input type="text" name="cord_city" id="cord_city" class="form-control" value="{{ $cdDetails->city }}"  required >
                                    </div>
                                    <div class="col-sm-3">
                                        <select name="cord_state" id="cord_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if(isset($cdDetails->state_id) && $cdDetails->state_id == $state->id) selected @endif>
                                                    {{$state->state_long_name}}
                                                </option>
                                    @endforeach
                                    </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <input type="text" name="cord_zip" id="cord_zip" class="form-control" value="{{ $cdDetails->zip }}"  required >
                                    </div>
                                    <div class="col-sm-2">
                                <select name="cord_country" id="cord_country" class="form-control" style="width: 100%;" required>
                                    <option value="">Select Country</option>
                                    @foreach($allCountries as $country)
                                            <option value="{{$country->id}}"
                                            @if(isset($cdDetails->country_id) && $cdDetails->country_id == $country->id) selected @endif>
                                                {{$country->name}}
                                            </option>
                                        @endforeach
                                </select>
                            </div>
                                </div>
                                 <!-- /.form group -->
                                 <div class="row mb-3">
                                    <label class="col-sm-2 col-form-label">Birthday:</label>
                                    <div class="col-sm-3">
                                        <select name="cord_month" class="form-control" style="width: 100%;" required>
                                            <option value="">Select Month</option>
                                            @foreach($allMonths as $month)
                                                <option value="{{$month->id}}"
                                                    @if($cdDetails->birthday_month_id == $month->id) selected @endif>
                                                    {{$month->month_long_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                    <input type="text" name="cord_day" id="cord_day" class="form-control" value="{{ $cdDetails->birthday_day }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                            <label class="col-sm-2">ForumList Subscriptions:</label>
                                        @php
                                            $Subscriptions = $cdDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                        @endphp
                                        <div class="col-sm-10">
                                            <div class="mb-1">You can always access all lists/posts through your MIMI profile, subscribing will allow you to receive individual emails when
                                                a new post is made.</div>
                                            <div class="mb-2">All coordinators are automatically subscribed to Public Announcements at the beginning of each fiscal year but are NOT automatically subscribed
                                                to CoordinatorList or BoardList.  If you'd like to udpate the settings for any list, simply subscribe or unsubscribe below.
                                            </div>
                                            <div class="mb-1"><b>Public Announcements:</b>
                                            {{ in_array(\App\Enums\ForumCategoryEnum::PUBLICLIST, $Subscriptions) ? 'SUBSCRIBED' : 'NOT SUBSCRIBED' }}
                                            @if (in_array(\App\Enums\ForumCategoryEnum::PUBLICLIST, $Subscriptions))
                                                <button type="button" class="btn btn-danger bg-gradient btn-xs ms-2" onclick="unsubscribe({{ \App\Enums\ForumCategoryEnum::PUBLICLIST }}, {{ $cdDetails->user_id }})"><i class="bi bi-ban me-2"></i>Unsubscribe</button>
                                            @else
                                                <button type="button" class="btn btn-success bg-gradient btn-xs ms-2" onclick="subscribe({{ \App\Enums\ForumCategoryEnum::PUBLICLIST }}, {{ $cdDetails->user_id }})"><i class="bi bi-check-lg me-2"></i>Subscribe</button>
                                            @endif
                                            </div>
                                            <div class="mb-1"><b>CoordinatorList:</b>
                                            {{ in_array(\App\Enums\ForumCategoryEnum::COORDLIST, $Subscriptions) ? 'SUBSCRIBED' : 'NOT SUBSCRIBED' }}
                                            @if (in_array(\App\Enums\ForumCategoryEnum::COORDLIST, $Subscriptions))
                                                <button type="button" class="btn btn-danger bg-gradient btn-xs ms-2" onclick="unsubscribe({{ \App\Enums\ForumCategoryEnum::COORDLIST }}, {{ $cdDetails->user_id }})"><i class="bi bi-ban me-2"></i>Unsubscribe</button>
                                            @else
                                                <button type="button" class="btn btn-success bg-gradient btn-xs ms-2" onclick="subscribe({{ \App\Enums\ForumCategoryEnum::COORDLIST }}, {{ $cdDetails->user_id }})"><i class="bi bi-check-lg me-2"></i>Subscribe</button>
                                            @endif
                                            </div>
                                            <div class="mb-1"><b>BoardList:</b>
                                            {{ in_array(\App\Enums\ForumCategoryEnum::BOARDLIST, $Subscriptions) ? 'SUBSCRIBED' : 'NOT SUBSCRIBED' }}
                                            @if (in_array(\App\Enums\ForumCategoryEnum::BOARDLIST, $Subscriptions))
                                                <button type="button" class="btn btn-danger bg-gradient btn-xs ms-2" onclick="unsubscribe({{ \App\Enums\ForumCategoryEnum::BOARDLIST }}, {{ $cdDetails->user_id }})"><i class="bi bi-ban me-2"></i>Unsubscribe</button>
                                            @else
                                                <button type="button" class="btn btn-success bg-gradient btn-xs ms-2" onclick="subscribe({{ \App\Enums\ForumCategoryEnum::BOARDLIST }}, {{ $cdDetails->user_id }})"><i class="bi bi-check-lg me-2"></i>Subscribe</button>
                                            @endif
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
                <button type="submit" class="btn btn-primary bg-gradient mb-2" onclick="return PreSaveValidate();"><i class="bi bi-floppy-fill me-2"></i>Save</button>
                <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="showChangePasswordAlert('{{ $cdDetails->user_id }}')"><i class="bi bi-lock-fill me-2" ></i>Change Password</button>
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

  //submit validation function
  function PreSaveValidate(){
          var NewPassword=document.getElementById("cord_pswd").value;
				//They changed their password
				if(document.getElementById("cord_pswd").value != document.getElementById("cord_pswd").getAttribute("value")){
					if(document.getElementById("cord_pswd").value != document.getElementById("cord_pswd_cnf").value){  //Make sure the password and confirmation match
						alert ("The provided passwords do not match, please re-enter your password.");
						document.getElementById("cord_pswd_cnf").focus();
						return false;
					}
					// Make sure the password is the right length
					else if(NewPassword.length < 7){
						alert("Password must be at least 7 characters.");
						document.getElementById("cord_pswd").focus();
						return false;
					}
					else{
						document.getElementById("cord_pswd_chg").value="1";
					}
                }
		//Okay, all validation passed, save the records to the database
		return true;
	}

</script>
@endsection
