@extends('layouts.board_theme')

<style>
.custom-switch .custom-control-label {
    color: #000 !important;
}
/* Or use the theme's default text color */
.custom-switch .custom-control-label {
    color: inherit !important;
}
</style>

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="col-md-12">
         <div class="card card-widget widget-user">
            <div class="widget-user-header bg-primary">
                <div class="widget-user-image">
                    <img class="img-circle elevation-2" src="{{ config('settings.base_url') }}images/logo-mimi.png" alt="MC" style="width: 115px; height: 115px;">
                  </div>
                </div>
                <div class="card-body">

                    <div class="col-md-12"><br><br></div>
                        <h2 class="text-center"> MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h2>
                        <h4 class="text-center">Mother-to-Mother Fund Grant Request Details</h4>
                             <h4 class="text-center">{{ $grantDetails->first_name }} {{ $grantDetails->last_name }}</h4>
                    <div class="col-md-12"><br></div>

                        </div>
                    </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

        <div class="container-fluid">
                   <form id="grant_request" name="grant_request" role="form" data-toggle="validator"
                    enctype="multipart/form-data" method="POST"
                    action='{{ route("board.updategrantrequest", ["id" => $grantDetails->id]) }}'>
                    @csrf

                    <input type="hidden" name="FurthestStep" id="FurthestStep" value="1">
                    <input type="hidden" name="submitted" id="submitted" value="{{ $grantDetails['submitted'] ?? '' }}" />

            <div class="row">
    <div class="col-12" id="accordion">
        <!------Start Step 1: Consent ------>
        <div class="card card-primary {{ $grantDetails->farthest_step_visited == '1' ? 'active' : '' }}">
            <div class="card-header" id="accordion-header-consent">
                <h4 class="card-title w-100">
                    <a class="d-block" data-toggle="collapse" href="#collapseConsent" style="width: 100%;">BEFORE YOU BEGIN</a>
                </h4>
            </div>
            <div id="collapseConsent" class="collapse {{ $grantDetails->farthest_step_visited == '1' ? 'show' : '' }}" data-parent="#accordion">
                <div class="card-body">
                    <section>
                        <p><strong>Please read this section before filling out the questions!</strong></p>
                        <p>If your chapter is requesting assistance from the Mother-to-Mother Fund for one of your members, please read the Mother-to-Mother Fund Fact Sheet. It contains important information on what kinds of grants can be given and what kinds cannot.</p>
                        <p>Before you ask for a grant, be sure the situation fits what we can help. There are many situations we cannot help with – divorce, unemployment, and birth defects are a few. We understand those are very difficult challenges for any mother, but they cannot be helped by the Fund.</p>
                        <p>If the situation might qualify for a grant, first ask the mother-in-need if she wants you to apply for her. Some people are very private. They do not want assistance nor for people to know they have a problem. If that is the case, we cannot give a grant. While we do not publish the names of grant recipients, we do publish information about the grants that are given, and it would be easy for people who know the mother to figure out if a grant was given and how much.</p>
                        <p>Only a chapter may apply for a grant for a member. The grant request should be filled out by a member of the Executive Board. That officer will be the liaison between the Mother-to-Mother Fund Committee and the mother-in-need. A mother-in-need may not apply for a grant on her own. The request has to come from the chapter, but the chapter may work with the mother to answer the questions here. If an officer is not available, due to a natural disaster or other problem, then another member may submit the request, but the Board will be contacted to confirm the information.</p>
                        <p>Be as specific as possible in answering the questions. Be sure to fill out all questions before submitting the form!</p>
                        <br>
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="understood" id="understood" class="custom-control-input" value="1"
                                {{ $grantDetails->understood == 1 ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="understood">
                                I have read this section and understand the limits of the fund<span class="field-required">*</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="member_agree" id="member_agree" class="custom-control-input" value="1"
                                {{ $grantDetails->member_agree == 1 ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="member_agree">
                                Some people do not want a grant request to be submitted for them. The mother has been asked if she wants you to submit this grant on her behalf<span class="field-required">*</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="member_accept" id="member_accept" class="custom-control-input" value="1"
                                {{ $grantDetails->member_accept == 1 ? 'checked' : '' }} disabled>
                            <label class="custom-control-label" for="member_accept">
                                The mother has agreed to accept a grant request if one is given<span class="field-required">*</span>
                            </label>
                        </div>
                    </div>
                    </section>
                </div>
            </div>
        </div>
        <!------End Step 1 ------>

        <!------Start Step 2: Submitter Information ------>
        <div class="card card-primary {{ $grantDetails->farthest_step_visited == '2' ? 'active' : '' }}">
            <div class="card-header" id="accordion-header-submitter">
                <h4 class="card-title w-100">
                    <a class="d-block" data-toggle="collapse" href="#collapseSubmitter" style="width: 100%;">CHAPTER/BOARD SUBMITTING REQUEST</a>
                </h4>
            </div>
            <div id="collapseSubmitter" class="collapse {{ $grantDetails->farthest_step_visited == '2' ? 'show' : '' }}" data-parent="#accordion">
                <div class="card-body">
                    <section>
                        <div class="col-12 form-row form-group">
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Chapter Name<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="chapter_name" id="chapter" value="{{ $grantDetails->chapters->name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Chapter State<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="chapter_name" id="chapter" value="{{$grantDetails->chapters->state->state_long_name}}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Board Member Name<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="submitter_name" id="submitter" value="{{ $grantDetails->board_name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Board Member Position<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="submitter_position" id="submitter" value="{{ $grantDetails->board_position }}" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Board Member Phone<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="submitter_name" id="submitter" value="{{ $grantDetails->board_phone }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Board Member Email<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="submitter_position" id="submitter" value="{{ $grantDetails->board_email }}" disabled>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <!------End Step 2 ------>

        <!------Start Step 3: Member Information ------>
        <div class="card card-primary {{ $grantDetails->farthest_step_visited == '3' ? 'active' : '' }}">
            <div class="card-header" id="accordion-header-member">
                <h4 class="card-title w-100">
                    <a class="d-block" data-toggle="collapse" href="#collapseMember" style="width: 100%;">MEMBER IN NEED</a>
                </h4>
            </div>
            <div id="collapseMember" class="collapse {{ $grantDetails->farthest_step_visited == '3' ? 'show' : '' }}" data-parent="#accordion">
                <div class="card-body">
                    <section>
                        <div class="col-12 form-row form-group">
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Member First Name<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="member_fname" value="{{ $grantDetails->first_name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Members Last Name<span class="field-required">*</span></label>
                                    <input type="text" class="form-control" name="member_lname" value="{{ $grantDetails->last_name }}" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Member Email<span class="field-required">*</span></label>
                                    <input type="email" class="form-control" name="member_email"  value="{{ $grantDetails->email }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 float-left">
                                <div class="form-group">
                                    <label>Member Phone<span class="field-required">*</span></label>
                                    <input type="tel" class="form-control" name="member_phone"  value="{{ $grantDetails->phone }}" required>
                                </div>
                            </div>
                        </div>

                     <div class="col-12 form-row form-group">
                    <div class="col-md-6 float-left">
                        <label>Can the member be reached at the number above?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-right: 20px;">
                                <input class="form-check-input" type="radio" id="ReachableYes" name="member_reachable" value="1"
                                    {{ $grantDetails->reachable == 1 ? 'checked' : '' }}
                                    required onchange="ToggleAlternatePhone()">
                                <label class="form-check-label" for="ReachableYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="ReachableNo" name="member_reachable" value="0"
                                    {{ !is_null($grantDetails->reachable) && $grantDetails->reachable == 0 ? 'checked' : '' }}
                                    onchange="ToggleAlternatePhone()">
                                <label class="form-check-label" for="ReachableNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 float-left" id="divAlternatePhone" style="display: {{ !is_null($grantDetails->reachable) && $grantDetails->reachable == 0 ? 'block' : 'none' }};">
                        <div class="form-group">
                            <label>Please provide an additional number<span class="field-required">*</span></label>
                            <input type="tel" class="form-control" name="member_alt_phone" value="{{ $grantDetails->alt_phone ?? '' }}">
                        </div>
                    </div>
                </div>

                        <div class="col-12 form-row form-group">
                            <label>Member Address<span class="field-required">*</span></label>
                            <div class="col-md-12 mb-1">
                                <input type="text" class="form-control" name="member_street" id="member_street" placeholder="Address" value="{{ $grantDetails->address }}" required>
                            </div>
                            <div class="col-md-4 mb-1 float-left">
                                <input type="text" class="form-control" name="member_city" id="member_city" placeholder="City" value="{{ $grantDetails->city }}" required>
                            </div>
                            <div class="col-md-3 mb-1 float-left">
                               <select name="member_state" id="member_state" class="form-control" style="width: 100%;" required>
                                     <option value="{{$grantDetails->state_id}}" selected>Select State</option>
                                    @foreach($allStates as $state)
                                        <option value="{{ $state->id }}" {{ $grantDetails->state_id == $state->id ? 'selected' : '' }}>
                                            {{ $state->state_long_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-1 float-left">
                                <input type="text" class="form-control" name="member_zip" id="member_zip" placeholder="ZIP Code" value="{{ $grantDetails->zip }}" required>
                            </div>
                            <div class="col-md-3 mb-1 float-left" id="member_country-container" style="display: none;">
                               <select name="member_country" id="member_country" class="form-control" style="width: 100%;">
                                    <option value="">Select Country</option>
                                    @foreach($allCountries as $country)
                                        <option value="{{ $country->id }}" {{ $grantDetails->country_id == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>How long has the mother-in-need been a member of your chapter? You may answer with a join date or the number of years/months she has been in your chapter.
                                        Is she a member now or has she "retired" or moved from your chapter?<span class="field-required">*</span></label>
                                    <textarea class="form-control" rows="2" name="member_length" required>{{ $grantDetails->member_length }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Who is living in the home? Is there a spouse? How many family members and what are the ages of the children?<span class="field-required">*</span></label>
                                    <textarea class="form-control" rows="2" name="household_members" required>{{ $grantDetails->household_members }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>If the member's home is uninhabitable, where is she living now? Please provide mailing address if different from above.<span class="field-required">*</span></label>
                                    <textarea class="form-control" rows="2" name="household_members" required>{{ $grantDetails->alt_address }}</textarea>
                                </div>
                            </div>
                        </div>

                         <div class="col-12 form-row form-group">
                        <label>Has the chapter ever asked for a grant for this mother or family in the past?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-right: 20px;">
                                <input class="form-check-input" type="radio" id="PreviousGrantYes" name="previous_grant" value="1" {{ $grantDetails->previous_grant == 1 ? 'checked' : '' }} required>
                                <label class="form-check-label" for="PreviousGrantYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="PreviousGrantNo" name="previous_grant" value="0" {{ !is_null($grantDetails->previous_grant) && $grantDetails->previous_grant == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="PreviousGrantNo">No</label>
                            </div>
                        </div>
                    </div>

                        <div class="card-body text-center">
                            <button type="button" id="btn-step-3" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save</button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <!------End Step 3 ------>

        <!------Start Step 4: Situation Details ------>
        <div class="card card-primary {{ $grantDetails->farthest_step_visited == '4' ? 'active' : '' }}">
            <div class="card-header" id="accordion-header-situation">
                <h4 class="card-title w-100">
                    <a class="d-block" data-toggle="collapse" href="#collapseSituation" style="width: 100%;">EXPLANATION OF SITUATION</a>
                </h4>
            </div>
            <div id="collapseSituation" class="collapse {{ $grantDetails->farthest_step_visited == '4' ? 'show' : '' }}" data-parent="#accordion">
                <div class="card-body">
                    <section>
                        <p><strong>Please be as specific as possible.</strong></p>
                        <p>Did their house catch on fire? If so, how did the fire start and what was lost? If someone has a life-threatening illness, be specific.
                            Don’t just say they are sick, but tell us what their illness is and how it is impacting the family and their finances.</p>
                        <p>if the need is for childcare while the mother is undergoing treatment, tell us how much that childcare will cost, how many weeks it will be needed,
                                    why they cannot afford it and why your chapter cannot help with that. If they cannot afford their medication, what medication do they need and how much would it cost them?
                                    If they need help traveling to treatment, where is the treatment, how many times will they need to go and how much will each trip cost?</p>
                        <p>The more specific information we have, the faster the Committee can make its decision.</p>
                        <br>
                       <div class="col-12 form-row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Please provide a summary of the situation. What happened, how did it happen and what is the result of it?<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="4" name="situation_summary" required>{{ $grantDetails->situation_summary }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>What has the family done to improve or handle the situation?<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="4" name="family_actions" required>{{ $grantDetails->family_actions }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>What is the financial situation of the family? Do they have insurance that will help with this? How much will it cover?
                                    Do they have savings? If so, how much? Are they getting help from their family or any other grants or loans?<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="4" name="financial_situation" required>{{ $grantDetails->financial_situation }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>What are the family’s most pressing needs right now? What are they having to do without because of this situation?<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="4" name="pressing_needs" required>{{ $grantDetails->pressing_needs }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Is there anything else that the family needs and is having to do without because of the situation?<span class="field-required">*</span></label>
                                <textarea class="form-control" rows="4" name="other_needs" required>{{ $grantDetails->other_needs }}</textarea>
                            </div>
                        </div>
                    </div>

                        <div class="card-body text-center">
                            <button type="button" id="btn-step-4" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save</button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <!------End Step 4 ------>

        <!------Start Step 5: Grant Request ------>
        <div class="card card-primary {{ $grantDetails->farthest_step_visited == '5' ? 'active' : '' }}">
            <div class="card-header" id="accordion-header-request">
                <h4 class="card-title w-100">
                    <a class="d-block" data-toggle="collapse" href="#collapseRequest" style="width: 100%;">GRANT REQUEST DETAILS</a>
                </h4>
            </div>
            <div id="collapseRequest" class="collapse {{ $grantDetails->farthest_step_visited == '5' ? 'show' : '' }}" data-parent="#accordion">
                <div class="card-body">
                    <section>
                        <div class="col-12 form-row form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>What amount is being requested? What will it be used for?<span class="field-required">*</span></label>
                                    <textarea class="form-control" rows="4" name="amount_requested" required>{{ $grantDetails->amount_requested }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>A chapter should always be the first ones to help a member-in-need. How has the chapter supported the member up to this point?
                                        Has the chapter done any fundraisers or made any donations to the family? What are the chapter’s future plans to help this family?<span class="field-required">*</span></label>
                                    <textarea class="form-control" rows="4" name="chapter_support" required>{{ $grantDetails->chapter_support }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 form-row form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Is there anything else we should know about this family or their situation?</label>
                                    <textarea class="form-control" rows="4" name="additional_info">{{ $grantDetails->additional_info }}</textarea>
                                </div>
                            </div>
                        </div>

                        @if ($grantDetails->photos_path != null)
                            <div class="col-md-12" id="PhotosBlock">
                                    <label class="mr-2">Photos of Damage Uploaded:</label><a href="https://drive.google.com/uc?export=download&id={{ $grantDetails['photos_path'] }}">View Photos</a><br>
                                    <strong style="color:red">Please Note</strong>
                                        This will refresh the screen - be sure to save all other work before clicking button to Replace Photos.<br>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showGrantUploadModal('{{ $grantDetails->id }}')"><i class="fas fa-upload mr-2"></i>Replace Photos</button>
                                    <br>
                                </div>
                        @else
                            <div class="col-md-12" id="PhotosBlock">
                                    <label class="mr-2">If there was damage to the member’s home or property, please upload any pictures here.</label><br>
                                    <strong style="color:red">Please Note</strong>
                                        This will refresh the screen - be sure to save all other work before clicking button to Upload Photos.<br>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="showGrantUploadModal('{{ $grantDetails->id }}')"><i class="fas fa-upload mr-2"></i>Upload Photos</button>
                                    <br>
                                </div>
                        @endif
                            <input type="hidden" name="PhotosPath" id="PhotosPath" value="{{ $grantDetails->photos_path }}">

                        <div class="card-body text-center">
                            <button type="button" id="btn-step-5" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Save</button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <!------End Step 5 ------>

        <!------Start Step 6: Final Affirmation ------>
        <div class="card card-primary {{ $grantDetails->farthest_step_visited == '6' ? 'active' : '' }}">
            <div class="card-header" id="accordion-header-affirmation">
                <h4 class="card-title w-100">
                    <a class="d-block" data-toggle="collapse" href="#collapseAffirmation" style="width: 100%;">CHAPTER BACKING & AFFIRMATION</a>
                </h4>
            </div>
            <div id="collapseAffirmation" class="collapse {{ $grantDetails->farthest_step_visited == '6' ? 'show' : '' }}" data-parent="#accordion">
                <div class="card-body">
                    <section>
                    <div class="col-12 form-row form-group">
                        <label>Does the chapter stand behind this request for a grant? Has the Executive Board discussed the situation and decided to submit this request?
                            And does the Executive Board assure the Mother-to-Mother Fund Committee that the information in this request is true?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-right: 20px;">
                                <input class="form-check-input" type="radio" id="BackingYes" name="chapter_backing" value="1" {{ $grantDetails->chapter_backing == 1 ? 'checked' : '' }} required>
                                <label class="form-check-label" for="BackingYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="BackingNo" name="chapter_backing" value="0" {{ !is_null($grantDetails->chapter_backing) && $grantDetails->chapter_backing == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="BackingNo">No</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 form-row form-group">
                        <label>Has the chapter donated to the Mother-to-Mother Fund in the past?<span class="field-required">*</span></label>
                        <div class="col-md-12 row">
                            <div class="form-check" style="margin-right: 20px;">
                                <input class="form-check-input" type="radio" id="DonationYes" name="m2m_donation" value="1" {{ $grantDetails->m2m_donation == 1 ? 'checked' : '' }} required>
                                <label class="form-check-label" for="DonationYes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="DonationNo" name="m2m_donation" value="0" {{ !is_null($grantDetails->m2m_donation) && $grantDetails->m2m_donation == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="DonationNo">No</label>
                            </div>
                        </div>
                    </div>
                      <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="affirmation" id="affirmation" class="custom-control-input" value="1"
                                {{ $grantDetails->affirmation == 1 ? 'checked' : '' }} required>
                            <label class="custom-control-label" for="affirmation">
                                I affirm that the information in this submission is true and the mother-in-need agrees with the submission and the information herein.<span class="field-required">*</span>
                            </label>
                        </div>
                    </div>

                        <div class="card-body text-center">
                            <button type="submit" id="btn-submit" class="btn btn-success"><i class="fas fa-share-square"></i>&nbsp; Submit Grant Request</button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <!------End Step 6 ------>

    </div><!-- end of accordion -->
</form>
            <div class="card-body text-center">

                @if ($userTypeId != \App\Enums\UserTypeEnum::OUTGOING && $userTypeId != \App\Enums\UserTypeEnum::DISBANDED)
                    @if ($userTypeId == \App\Enums\UserTypeEnum::COORD)
                        <button type="button" id="btn-back" class="btn btn-primary m-1" onclick="window.location.href='{{ route('board.editprofile', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Profile</button>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary m-1"><i class="fas fa-reply mr-2" ></i>Back to Profile</a>
                    @endif
                @endif
                    <button type="button" id="btn-back" class="btn btn-primary m-1" onclick="window.location.href='{{ route('board.viewgrantrequestlist', ['id' => $chDetails->id]) }}'"><i class="fas fa-reply mr-2" ></i>Back to Grant List</button>
            </div>

        <!-- End Modal Popups -->
    </div>
</div>
@endsection
@section('customscript')
<script>
    /* Disable fields and buttons  */
$(document).ready(function () {
    setTimeout(function () {
        var submitted = @json($grantDetails->submitted);
        var userTypeId = @json($userTypeId);
        var userAdmin = @json($userAdmin);

        if (userTypeId == 1 && userAdmin != 1) {
            $('button, input, select, textarea').not('#btn-back').prop('disabled', true);
        } else if (submitted == 1) {
            $('button, input, select, textarea').not('#btn-back').prop('disabled', true);
        } else {
            // Enable all fields except btn-back
            $('button, input, select, textarea').prop('disabled', false);
            // Then specifically disable the fields that should always be disabled
            $('#understood, #member_agree, #member_accept, #chapter, #submitter').prop('disabled', true);
        }

    }, 1000); // 1-second delay
});
</script>

<script>
function ToggleAlternatePhone() {
    var reachable = document.querySelector('input[name="member_reachable"]:checked');
    var divAlternatePhone = document.getElementById('divAlternatePhone');

    if (reachable && reachable.value == '0') {  // Changed from 'no' to '0'
        divAlternatePhone.style.display = 'block';
    } else {
        divAlternatePhone.style.display = 'none';
    }
}

// Initialize on page load
window.addEventListener('load', function() {
    ToggleAlternatePhone();
});

document.addEventListener('DOMContentLoaded', function() {
// Chapter state and country
    const stateDropdown = document.getElementById('member_state');
    const countryContainer = document.getElementById('member_country-container');
    const countrySelect = document.getElementById('member_country');

    // Check if elements exist before adding listeners
    if (stateDropdown && countryContainer && countrySelect) {
        // Initially set country field requirement based on state selection
        toggleCountryField();

        // Add event listener to the state dropdown
        stateDropdown.addEventListener('change', toggleCountryField);

        function toggleCountryField() {
            const selectedStateId = parseInt(stateDropdown.value) || 0;
            const specialStates = [52, 53, 54, 55]; // States that should show the country field

            if (specialStates.includes(selectedStateId)) {
                countryContainer.style.display = 'flex'; // or 'block' depending on your layout
                countrySelect.setAttribute('required', 'required');
            } else {
                countryContainer.style.display = 'none';
                countrySelect.removeAttribute('required');
                // Optionally clear the country selection when hidden
                countrySelect.value = "";
            }
        }
    }
});

</script>
<script>
 /* Save & Submit Verification */
$(document).ready(function() {
    function submitFormWithStep(step) {
        $("#submitted").val('0');  // Add this - ensure it's saving, not submitting
        $("#FurthestStep").val(step);
        // Trigger the form's submit event properly (not .submit() method)
        var form = document.getElementById('grant_request');
        var event = new Event('submit', { cancelable: true, bubbles: true });
        form.dispatchEvent(event);
    }

    $("#btn-step-3").click(function() {
        if (!EnsureMemberInformation()) return false;
        submitFormWithStep(3);
    });
    $("#btn-step-4").click(function() {
        if (!EnsureSituationExplanation()) return false;
        submitFormWithStep(4);
    });
    $("#btn-step-5").click(function() {
        if (!EnsureGrantRequest()) return false;
        submitFormWithStep(5);
    });
});

$("#btn-submit").click(function(e) {
    e.preventDefault();  // Add this to prevent double submission

    // Validation checks
    if (!EnsureMemberInformation()) return false;
    if (!EnsureSituationExplanation()) return false;
    if (!EnsureGrantRequest()) return false;
    if (!EnsureAffirmation()) return false;

    // Use SweetAlert2 for the final confirmation
    Swal.fire({
        title: 'Grant Submission',
        text: "This will finalize and submit your grant request. You will no longer be able to edit this form. Do you wish to continue?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Submit Request',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn-sm btn-success',
            cancelButton: 'btn-sm btn-danger'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show processing spinner
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
                customClass: {
                    confirmButton: 'btn-sm btn-success'
                }
            });

            // Set values and trigger form submit properly
            $("#submitted").val('1');
            $("#FurthestStep").val('6');

            // Trigger the form's submit event properly (not .submit() method)
            var form = document.getElementById('grant_request');
            form.submit();  // Use direct submit here since we already validated
        }
    });
});

function EnsureMemberInformation() {
    var missingFields = [];

    if (!document.querySelector('input[name="member_email"]').value.trim()) {
        missingFields.push("Member Email");
    }
    if (!document.querySelector('input[name="member_phone"]').value.trim()) {
        missingFields.push("Member Phone");
    }

    // Check if reachable radio is selected
    if (!document.querySelector('input[name="member_reachable"]:checked')) {
        missingFields.push("Can the member be reached at the number above?");
    } else {
        // If "0" (No) is selected, check for alternate phone
        var reachable = document.querySelector('input[name="member_reachable"]:checked').value;
        if (reachable == '0') {  // Changed from === 'no' to == '0'
            var altPhone = document.querySelector('input[name="member_alt_phone"]');
            if (!altPhone || !altPhone.value.trim()) {
                missingFields.push("Alternate Phone Number (required when member cannot be reached at primary number)");
            }
        }
    }

    if (!document.querySelector('input[name="member_street"]').value.trim()) {
        missingFields.push("Member Address");
    }
    if (!document.querySelector('input[name="member_city"]').value.trim()) {
        missingFields.push("City");
    }
    if (!document.querySelector('select[name="member_state"]').value) {
        missingFields.push("State");
    }
    if (!document.querySelector('input[name="member_zip"]').value.trim()) {
        missingFields.push("ZIP Code");
    }

    // Check if country is required (based on state selection)
    var countryContainer = document.getElementById('member_country-container');
    if (countryContainer && countryContainer.style.display !== 'none') {
        if (!document.querySelector('select[name="member_country"]').value) {
            missingFields.push("Country");
        }
    }

    if (!document.querySelector('textarea[name="member_length"]').value.trim()) {
        missingFields.push("How long has the mother been a member");
    }
    if (!document.querySelector('textarea[name="household_members"]').value.trim()) {
        missingFields.push("Who is living in the home");
    }

    if (missingFields.length > 0) {
        var missingFieldsText = missingFields.map(field => `<li>${field}</li>`).join('');
        var message = `<p>The following fields in the MEMBER-IN-NEED INFORMATION section are required:</p>
                      <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                          ${missingFieldsText}
                      </ul>`;
        customWarningAlert(message);
        return false;
    }

    return true;
}

function EnsureSituationExplanation() {
    var missingFields = [];

    if (!document.querySelector('textarea[name="situation_summary"]').value.trim()) {
        missingFields.push("Summary of the situation");
    }
    if (!document.querySelector('textarea[name="family_actions"]').value.trim()) {
        missingFields.push("What has the family done to improve or handle the situation");
    }
    if (!document.querySelector('textarea[name="financial_situation"]').value.trim()) {
        missingFields.push("Financial situation of the family");
    }
    if (!document.querySelector('textarea[name="pressing_needs"]').value.trim()) {
        missingFields.push("Family's most pressing needs");
    }
    if (!document.querySelector('textarea[name="other_needs"]').value.trim()) {
        missingFields.push("Is there anything else the family needs");
    }

    if (missingFields.length > 0) {
        var missingFieldsText = missingFields.map(field => `<li>${field}</li>`).join('');
        var message = `<p>The following fields in the EXPLANATION OF SITUATION section are required:</p>
                      <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                          ${missingFieldsText}
                      </ul>`;
        customWarningAlert(message);
        return false;
    }

    return true;
}

function EnsureGrantRequest() {
    var missingFields = [];

    if (!document.querySelector('textarea[name="amount_requested"]').value.trim()) {
        missingFields.push("What amount is being requested and what will it be used for");
    }
    if (!document.querySelector('textarea[name="chapter_support"]').value.trim()) {
        missingFields.push("How has the chapter supported the member");
    }

    if (missingFields.length > 0) {
        var missingFieldsText = missingFields.map(field => `<li>${field}</li>`).join('');
        var message = `<p>The following fields in the GRANT REQUEST DETAILS section are required:</p>
                      <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                          ${missingFieldsText}
                      </ul>`;
        customWarningAlert(message);
        return false;
    }

    return true;
}

function EnsureAffirmation() {
    var missingFields = [];

    if (!document.querySelector('input[name="previous_grant"]:checked')) {
        missingFields.push("Has the chapter ever asked for a grant for this mother or family in the past?");
    }
    if (!document.querySelector('input[name="chapter_backing"]:checked')) {
        missingFields.push("Does the chapter stand behind this request?");
    }
    if (!document.querySelector('input[name="m2m_donation"]:checked')) {
        missingFields.push("Has the chapter donated to the Mother-to-Mother Fund?");
    }
    if (!document.querySelector('input[name="affirmation"]:checked')) {
        missingFields.push("I affirm that the information in this submission is true");
    }

    if (missingFields.length > 0) {
        var missingFieldsText = missingFields.map(field => `<li>${field}</li>`).join('');
        var message = `<p>The following items in the CHAPTER BACKING & AFFIRMATION section are required:</p>
                      <ul style="list-style-position: inside; padding-left: 0; margin-left: 0;">
                          ${missingFieldsText}
                      </ul>`;
        customWarningAlert(message);
        return false;
    }

    return true;
}

// Helper function for custom warning alert (assuming you have this already)
function customWarningAlert(message) {
    Swal.fire({
        icon: 'warning',
        title: 'Required Information Missing',
        html: message,
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn-sm btn-primary'
        }
    });
}
</script>
@endsection


