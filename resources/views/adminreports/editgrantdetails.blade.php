@extends('layouts.coordinator_theme')

@section('page_title', 'Chapter Details')
@section('breadcrumb', 'Board Information')
<style>
.disabled-link {
    pointer-events: none; /* Prevent click events */
    cursor: default; /* Change cursor to default */
    color: #343a40; /* Font color */
}

.custom-span {
    border: none !important;
    background-color: transparent !important;
    padding: 0.375rem 0 !important; /* Match the vertical padding of form-control */
    box-shadow: none !important;
}


</style>
@section('content')
    <!-- Main content -->
    <form class="form-horizontal" method="POST" action='{{ route("chapters.updatepending", $chDetails->id) }}'>
    @csrf
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-4">


            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                 <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                <br>
                  </p>


                   <div class="form-group row mt-1">
                    <label class="col-form-label col-sm-6">Region:</label>
                    <div class="col-sm-6">

                    </div>
                </div>

                <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="understood" id="understood" class="custom-control-input" value="1"
                                {{ $grantDetails->approved == 1 ? 'checked' : '' }} >
                            <label class="custom-control-label" for="understood">
                                Approved<span class="field-required">*</span>
                            </label>
                        </div>
                    </div>

                  <ul class="list-group list-group-unbordered mb-3">

                    <input type="hidden" id="ch_primarycor" value="{{ $chDetails->primary_coordinator_id }}">
                    <li class="list-group-item" id="display_corlist" class="list-group-item"></li>

                             <li class="list-group-item">

                  <div class="text-center">
                      @if ($chDetails->active_status == 1 )
                          <b><span style="color: #28a745;">Chapter is ACTIVE</span></b>
                      @elseif ($chDetails->active_status == 2)
                        <b><span style="color: #ff851b;">Chapter is PENDING</span></b>
                      @elseif ($chDetails->active_status == 3)
                        <b><span style="color: #dc3545;">Chapter was NOT APPROVED</span></b><br>
                          Declined Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @elseif ($chDetails->active_status == 0)
                          <b><span style="color: #dc3545;">Chapter is NOT ACTIVE</span></b><br>
                          Disband Date: <span class="date-mask">{{ $chDetails->zap_date }}</span><br>
                          {{ $chDetails->disband_reason }}
                      @endif
                  </div>
                </li>

                   @if ($chDetails->active_status == '2')
                    <li class="list-group-item">
                        <div class="card-body text-center">
                                <button type="button" class="btn bg-gradient-primary mb-3"
                                    onclick="showChapterSetupEmailModal({{ $chDetails->id }}, '{{ $userName }}', '{{ $userPosition }}', '{{ $userConfName }}', '{{ $userConfDesc }}')">
                                    <i class="fas fa-envelope mr-2"></i>Send Startup Email</button>
                            <button type="submit" class="btn bg-gradient-primary mb-3" ><i class="fas fa-save mr-2"></i>Save Updates</button>
                            <br>
                            Save all changes before approval!
                            <br>
                             <button type="button" class="btn bg-gradient-success" onclick="chapApprove({{ $chDetails->id }}, '{{ $chDetails->region_id }}')"><i class="fas fa-check mr-2"></i>Approve Chapter</button>
                            <button type="button" class="btn bg-gradient-danger" onclick="chapDecline({{ $chDetails->id }})"><i class="fas fa-times mr-2"></i>Decline Chaper</button>
                    </li>
                @endif
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
                <h3 class="profile-username">GRANT INFORMATION</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                        <div class="mb-1">
                            <label class="mr-2">I have read this section and understand the limits of the fund:</label>
                            {{ is_null($grantDetails['understood']) ? 'Not Answered' : ($grantDetails['understood'] == 0 ? 'NO' : ($grantDetails['understood'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>
                        <div class="mb-1">
                            <label class="mr-2">The mother has been asked if she wants you to submit this grant on her behalf:</label>
                            {{ is_null($grantDetails['member_agree']) ? 'Not Answered' : ($grantDetails['member_agree'] == 0 ? 'NO' : ($grantDetails['member_agree'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>
                        <div class="mb-1">
                            <label class="mr-2">The mother has agreed to accept a grant request if one is given:</label>
                            {{ is_null($grantDetails['member_accept']) ? 'Not Answered' : ($grantDetails['member_accept'] == 0 ? 'NO' : ($grantDetails['member_accept'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>

                        </div>
                    </div>

                    <hr>

                <h3 class="profile-username">BOARD MEMBER SUBMITTING REQUEST</h3>
                <!-- /.card-header -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Board Member Name:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $grantDetails->first_name}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Board Member Position:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $grantDetails->first_name}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Board Member Email:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $grantDetails->first_name}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Board Member Phone:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $grantDetails->first_name}}
                            </div>
                        </div>
                   </div>
            </div>

     <hr>

<h3 class="profile-username">MEMBER IN NEED</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                        <div class="row">
                            <div class="col-md-3">
                                <label>Name:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $grantDetails->first_name}} {{ $grantDetails->last_name}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Address:</label>
                            </div>
                            <div class="col-md-9">
                                {{ $grantDetails->address}}<br>
                                @if( $grantDetails->city != null){{ $grantDetails->city}}, @endif{{ $grantDetails->state?->state_short_name}} {{ $grantDetails->zip}}<br>
                                {{ $grantDetails->country?->short_name}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="mb-0">How long has the mother been a member?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->member_length != null)
                                {{ $grantDetails->member_length}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        </div>
                    </div>

                    <hr>

                    <h3 class="profile-username">GRANT REQUEST DETAILS</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                            <div class="row">
                            <div class="col-md-12">
                                <label class="mb-0">How long has the mother been a member?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->member_length != null)
                                {{ $grantDetails->member_length}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>


                        </div>
                    </div>

                    <hr>

                    <h3 class="profile-username">CHAPTER BACKING & AFFIRMATION</h3>
                    <!-- /.card-header -->
                    <div class="row">
                        <div class="col-md-12">

                          <div class="row">
                            <div class="col-md-12">
                                <label class="mb-0">How long has the mother been a member?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->member_length != null)
                                {{ $grantDetails->member_length}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label class="mb-0">Who is living in the home?</label>
                            </div>
                            <div class="col-md-12">
                                @if ($grantDetails->household_members != null)
                                {{ $grantDetails->household_members}}
                                @else
                                Not Answered
                                @endif
                            </div>
                        </div>

                        <div class="mb-1 mt-2">
                            <label class="mr-2">I affirm that the information in this submission is true:</label>
                            {{ is_null($grantDetails['affirmation']) ? 'Not Answered' : ($grantDetails['affirmation'] == 0 ? 'NO' : ($grantDetails['affirmation'] == 1 ? 'YES' : 'Not Answered')) }}
                        </div>

                        </div>
                    </div>

                    <hr>



                </div>
              <!-- /.card-body -->
                        </div>
            <!-- /.card -->
                      </div>
          <!-- /.col -->

          <div class="col-md-12">
            <div class="card-body text-center">


            </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </form>
    </section>
    <!-- /.content -->
@endsection
@section('customscript')
    @include('layouts.scripts.disablefields')

@endsection
