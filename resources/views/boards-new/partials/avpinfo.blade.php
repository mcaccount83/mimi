@if ($AVPDetails->user_id == '')
    <div class="col-12">
        <h4>Administrative Vice President Position is Vacant</h4>
    </div>
@else
    <div class="col-md-6">
        <h4 class="mb-0">{{$AVPDetails->first_name}} {{$AVPDetails->last_name}}</h4>
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
