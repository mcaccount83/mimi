@if ($SECDetails->user_id == '')
    <div class="col-12">
        <h4>Secretary Position is Vacant</h4>
    </div>
@else
    <div class="col-md-6">
        <h4 class="mb-0">{{$SECDetails->first_name}} {{$SECDetails->last_name}}</h4>
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
