<div class="row">
    <div class="col-md-6">
        <h3 class="mb-2">{{$Details->first_name}} {{$Details->last_name}}
        <span class="badge badge-outline-primary badge-inherit-size ms-2" style="cursor: pointer;"
              onclick="window.location.href='{{ route('impersonate.start', ['userId' => $Details->user_id]) }}'">
            View as {{ $Details->first_name }} {{ $Details->last_name }}</span>
        </h3>
        @mailto($Details->email)
        <br>
        @tel($Details->phone)
        <br>
        {{$Details->street_address}}
        <br>
        {{$Details->city}},{{$Details->state->state_short_name}}&nbsp;{{$Details->zip}}
        <br>
        {{$Details->country->short_name}}
    </div>
    <div class="col-md-6"></div>
</div>
<div class="row mt-3">
    @php
        $Subscriptions = $Details->user?->categorySubscriptions?->pluck('category_id')->toArray() ?? [];
    @endphp
    <dt class="col-sm-3">Public Announcements:</dt>
    <dd class="col-sm-9">
        @if(in_array(\App\Enums\ForumCategoryEnum::PUBLICLIST, $Subscriptions))
            <span class="badge bg-success badge-inherit-size ms-2">SUBSCRIBED</span>
            <i class="bi bi-ban text-danger" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="right" title="Unsubscribe from List"
               onclick="unsubscribe({{ \App\Enums\ForumCategoryEnum::PUBLICLIST }}, {{ $Details->user_id }})"></i>
        @else
            <span class="badge bg-primary badge-inherit-size ms-2" style="cursor: pointer;" onclick="subscribe({{ \App\Enums\ForumCategoryEnum::PUBLICLIST }}, {{ $Details->user_id }})">SUBSCRIBE NOW</span>
        @endif
    </dd>
    <dt class="col-sm-3">BoardList</dt>
    <dd class="col-sm-9">
        @if(in_array(\App\Enums\ForumCategoryEnum::BOARDLIST, $Subscriptions))
            <span class="badge bg-success badge-inherit-size ms-2">SUBSCRIBED</span>
            <i class="bi bi-ban text-danger" style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="right" title="Unsubscribe from List"
               onclick="unsubscribe({{ \App\Enums\ForumCategoryEnum::BOARDLIST }}, {{ $Details->user_id }})"></i>
        @else
            <span class="badge bg-primary badge-inherit-size ms-2" style="cursor: pointer;" onclick="subscribe({{ \App\Enums\ForumCategoryEnum::BOARDLIST }}, {{ $Details->user_id }})">SUBSCRIBE NOW</span>
        @endif
    </dd>
    <div class="col-md-12">
        <p>This will reset password to default "TempPass4You" for this user only.
        <br>
        <span class="badge bg-primary reset-password-btn keep-enabled badge-inherit-size" style="cursor: pointer;" data-user-id="{{ $Details->user_id }}">Reset {{ $Details->first_name }}'s Password</span>
        </p>
    </div>
</div>
