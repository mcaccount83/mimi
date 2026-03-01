<div class="col-md-6">
    <h4 class="mb-0">{{$PresDetails->first_name}} {{$PresDetails->last_name}}</h4>
    <a href="mailto:{{ $PresDetails->email }}">{{ $PresDetails->email }}</a>
    <br>
    <span class="phone-mask">{{$PresDetails->phone }}</span>
    <br>
    {{$PresDetails->street_address}}
    <br>
    {{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}
        <br>
    {{$PresDetails->country->short_name}}
</div>
