@if ($TRSDetails->user_id == '')
    <div class="col-12">
        <h4>Treasurer Position is Vacant</h4>
    </div>
@else
    <div class="col-md-6">
        <h4 class="mb-0">{{$TRSDetails->first_name}} {{$TRSDetails->last_name}}</h4>
        <a href="mailto:{{ $TRSDetails->email }}">{{ $TRSDetails->email }}</a>
        <br>
        <span class="phone-mask">{{$TRSDetails->phone}}</span>
        <br>
        {{$TRSDetails->street_address}}
        <br>
        {{$TRSDetails->city}},{{$TRSDetails->state?->state_short_name}}&nbsp;{{$TRSDetails->zip}}
        <br>
        {{$TRSDetails->country?->short_name}}
    </div>
@endif
