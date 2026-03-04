@if ($MVPDetails->user_id == '')
    <div class="col-12">
        <label>Membership Vice President Position is Vacant</label>
    </div>
@else
    <div class="col-md-6">
        <label>{{$MVPDetails->first_name}} {{$MVPDetails->last_name}}</label>
        <br>
        <a href="mailto:{{ $MVPDetails->email }}">{{ $MVPDetails->email }}</a>
        <br>
        <span class="phone-mask">{{$MVPDetails->phone}}</span>
        <br>
        {{$MVPDetails->street_address}}
        <br>
        {{$MVPDetails->city}},{{$MVPDetails->state?->state_short_name}}&nbsp;{{$MVPDetails->zip}}
        <br>
        {{$MVPDetails->country?->short_name}}
    </div>
@endif
