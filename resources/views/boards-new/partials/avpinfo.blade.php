@if ($AVPDetails->user_id == '')
    <div class="col-12">
        <label>Administrative Vice President Position is Vacant</label>
    </div>
@else
    <div class="col-md-6">
        <label>{{$AVPDetails->first_name}} {{$AVPDetails->last_name}}</label>
        <br>
        <a href="mailto:{{ $AVPDetails->email }}">{{ $AVPDetails->email }}</a>
        <br>
        <span class="phone-mask">{{$AVPDetails->phone}}</span>
        <br>
        {{$AVPDetails->street_address}}
        <br>
        {{$AVPDetails->city}},{{$AVPDetails->state?->state_short_name}}&nbsp;{{$AVPDetails->zip}}
        <br>
        {{$AVPDetails->country?->short_name}}
    </div>
@endif
