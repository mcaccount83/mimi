@extends('layouts.public_theme')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h3 class="profile-username text-center">MOMS Club of {{ $chDetails->name }}, {{$stateShortName}}</h3>
                <p class="text-center">{{ $conferenceDescription }} Conference, {{ $regionLongName }} Region
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <label>Chapter Boundaries:</label> {{ $chDetails->territory}}
                <br>
                    <label>Chapter Contact Email:</label> <a href="mailto:{{ $chDetails->inquiries_contact}}">{{ $chDetails->inquiries_contact}}</a>
                <br>
                <label>Chapter Website:</label>
                    @if($chDetails->website_url == 'http://' || empty($chDetails->website_url))
                        &nbsp;
                    @else
                        <a href="{{$chDetails->website_url}}" target="_blank">{{$chDetails->website_url}}</a>
                    @endif
                <br>

        </div>
    </div>

</div>
@endsection

@section('customscript')
<script>

</script>
