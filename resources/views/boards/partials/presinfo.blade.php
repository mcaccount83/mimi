<div class="col-md-6">
    <label>{{$PresDetails->first_name}} {{$PresDetails->last_name}}</label>
    <br>
    @mailto($PresDetails->email)
    <br>
    @tel($PresDetails->phone)
    <br>
    {{$PresDetails->street_address}}
    <br>
    {{$PresDetails->city}},{{$PresDetails->state->state_short_name}}&nbsp;{{$PresDetails->zip}}
        <br>
    {{$PresDetails->country->short_name}}
</div>
