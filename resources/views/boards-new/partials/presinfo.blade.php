<div class="col-md-6">
    <label>{{$PresDetails->first_name}} {{$PresDetails->last_name}}</label>
    <br>
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
