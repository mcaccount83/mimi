@if ($TRSDetails->user_id == '')
    <div class="col-12">
        <label>Treasurer Position is Vacant</label>
    </div>
@else
    <div class="col-md-6">
        <label>{{$TRSDetails->first_name}} {{$TRSDetails->last_name}}</label>
        <br>
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
