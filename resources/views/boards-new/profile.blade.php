@extends('layouts.mimi_theme')

@section('page_title', 'MOMS Club of ' . $chDetails->name . ', ' . $stateShortName)
@section('breadcrumb', 'User Profile')

@section('content')
     <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
        <div class="col-12">

                    <form method="POST" action='{{ route("board-new.updateprofile", $chDetails->id) }}' autocomplete="off">
                        @csrf

<div class="col-md-12">
            <div class="card card-primary card-outline">
                    <div class="card-body">
                        <div class="card-header bg-transparent border-0">
                            <h3>User Profile</h3>

                        </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                        <div class="row mb-3">
                            <label class="col-sm-2 mb-1 col-form-label">Contact Information:</label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_bor_fname" id="ch_bor_fname" class="form-control" value="{{ $borDetails->first_name }}" required placeholder="First Name" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_bor_lname" id="ch_bor_lname" class="form-control" value="{{ $borDetails->last_name }}" required placeholder="Last Name">
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_bor_email" id="ch_bor_email" class="form-control" value="{{ $borDetails->email }}" required placeholder="Email Address" >
                            </div>
                            <div class="col-sm-5 mb-1">
                            <input type="text" name="ch_bor_phone" id="ch_bor_phone" class="form-control" data-inputmask='"mask": "(999) 999-9999"' data-mask value="{{ $borDetails->phone }}" required placeholder="Phone Number" >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"></label>
                            <div class="col-sm-10 mb-1">
                            <input type="text" name="ch_bor_street" id="ch_bor_street" class="form-control" placeholder="Address" value="{{ $borDetails->street_address }}" required >
                            </div>
                            <label class="col-sm-2 mb-1 col-form-label"><br></label>
                            <div class="col-sm-3 mb-1">
                             <input type="text" name="ch_bor_city" id="ch_bor_city" class="form-control" value="{{ $borDetails->city }}"  required placeholder="City">
                                </div>
                                <div class="col-sm-3 mb-1">
                                    <select name="ch_bor_state" id="ch_bor_state" class="form-control" style="width: 100%;" required>
                                        <option value="">Select State</option>
                                        @foreach($allStates as $state)
                                        <option value="{{$state->id}}"
                                            @if($borDetails->state_id == $state->id) selected @endif>
                                            {{$state->state_long_name}}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2 mb-1">
                                    <input type="text" name="ch_bor_zip" id="ch_bor_zip" class="form-control" value="{{ $borDetails->zip }}"  required placeholder="Zip">
                                </div>
                                <div class="col-sm-2" id="ch_bor_country-container" style="display: none;">
                                    <select name="ch_bor_country" id="ch_bor_country" class="form-control" style="width: 100%;" required>
                                        <option value="">Select Country</option>
                                        @foreach($allCountries as $country)
                                        <option value="{{$country->id}}"
                                            @if($borDetails->country_id == $country->id) selected @endif>
                                            {{$country->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                         <div class="row mb-3">
                            <label class="col-sm-2">ForumList Subscriptions:</label>
                                        @php
                                            $Subscriptions = $borDetails->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
                                        @endphp
                                        <div class="col-sm-10">
                                            <div class="mb-1">You can always access all Public Announcement & BoardList posts through your MIMI profile, subscribing will allow you to receive individual emails when
                                                a new post is made.</div>
                                            <div class="mb-2">All board members are automatically subscribed to Public Announcements and BoardList at the beginning of each fiscal year.
                                                If you'd like to udpate the settings for either list, simply subscribe or unsubscribe below.
                                            </div>
                                            <div class="mb-1"><b>Public Announcements:</b>
                                            {{ in_array(\App\Enums\ForumCategoryEnum::PUBLICLIST, $Subscriptions) ? 'SUBSCRIBED' : 'NOT SUBSCRIBED' }}
                                            @if (in_array(\App\Enums\ForumCategoryEnum::PUBLICLIST, $Subscriptions))
                                                <button type="button" class="btn btn-danger bg-gradient btn-xs ms-2" onclick="unsubscribe({{ \App\Enums\ForumCategoryEnum::PUBLICLIST }}, {{ $borDetails->user_id }})"><i class="bi bi-ban me-2"></i>Unsubscribe</button>
                                            @else
                                                <button type="button" class="btn btn-success bg-gradient btn-xs ms-2" onclick="subscribe({{ \App\Enums\ForumCategoryEnum::PUBLICLIST }}, {{ $borDetails->user_id }})"><i class="bi bi-check-lg me-2"></i>Subscribe</button>
                                            @endif
                                            </div>
                                            <div class="mb-1"><b>BoardList:</b>
                                            {{ in_array(\App\Enums\ForumCategoryEnum::BOARDLIST, $Subscriptions) ? 'SUBSCRIBED' : 'NOT SUBSCRIBED' }}
                                            @if (in_array(\App\Enums\ForumCategoryEnum::BOARDLIST, $Subscriptions))
                                                <button type="button" class="btn btn-danger bg-gradient btn-xs ms-2" onclick="unsubscribe({{ \App\Enums\ForumCategoryEnum::BOARDLIST }}, {{ $borDetails->user_id }})"><i class="bi bi-ban me-2"></i>Unsubscribe</button>
                                            @else
                                                <button type="button" class="btn btn-success bg-gradient btn-xs ms-2" onclick="subscribe({{ \App\Enums\ForumCategoryEnum::BOARDLIST }}, {{ $borDetails->user_id }})"><i class="bi bi-check-lg me-2"></i>Subscribe</button>
                                            @endif
                                            </div>
                                        </div>

                        </div>

                        <div class="col-md-12">
                            <div class="card-body text-center mt-3">
                            <button type="submit" id="Save" class="btn btn-primary bg-gradient mb-2" onclick="return validateEmailsBeforeSubmit();"><i class="bi bi-floppy-fill me-2"></i>Save</button>
                            <button type="button" class="btn btn-primary bg-gradient mb-2" onclick="showChangePasswordAlert('{{ $borDetails->user_id }}')"><i class="bi bi-lock-fill me-2" ></i>Change Password</button>
                            </div>
                        </div>

                    </div>
            </div>
            <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

    </form>

            </div>
            <!-- /.col -->
       </div>
    </div>
    <!-- /.container- -->
@endsection
@section('customscript')
@if($userTypeId == \App\Enums\UserTypeEnum::COORD)
    @php $disableMode = 'disable-all'; @endphp
    @include('layouts.scripts.disablefields')
@endif
@endsection
