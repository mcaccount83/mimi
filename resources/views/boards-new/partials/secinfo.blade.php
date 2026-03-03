@if ($SECDetails->user_id == '')
    <div class="col-12">
        <label>Secretary Position is Vacant</label>
    </div>
@else
    <div class="col-md-6">
        <label>{{$SECDetails->first_name}} {{$SECDetails->last_name}}</label>
        <br>
        <a href="mailto:{{ $SECDetails->email }}">{{ $SECDetails->email }}</a>
        <br>
        <span class="phone-mask">{{$SECDetails->phone}}</span>
        <br>
        {{$SECDetails->street_address}}
        <br>
        {{$SECDetails->city}},{{$SECDetails->state?->state_short_name}}&nbsp;{{$SECDetails->zip}}
        <br>
        {{$SECDetails->country?->short_name}}
    </div>
@endif
