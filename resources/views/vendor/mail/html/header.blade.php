<tr>
<td class="header">
    <a href="{{ $url }}" style="display:inline-block;">
        {{-- <img src="{{ config('app.url') }}/public/images/logo-moms.png" alt="International MOMS Club" style="width: 125px;"> --}}

        {{-- <img src="{{ config('settings.base_url') }}images/logo-long.png" alt="International MOMS Club" style="width: 250px;"> --}}

        <img src="{{ $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim(config('settings.base_url'), '/') . 'images/logo-long.png' }}" alt="International MOMS Club" style="width: 250px;">

        {{-- <a href="{{ $url }}" style="display:inline-block; font-family: Arial, sans-serif; font-size:18px; font-weight:bold; color:#0d3349; text-decoration:none;">
            <img src="{{ config('settings.base_url') }}images/logo-long.png"
                alt=""
                width="250"
                style="max-height:60px; width:auto; border:0; display:block;">
            International MOMS Club
        </a> --}}

    {{-- <img src="https://momsclub.org/images/logo.png"
         alt="International MOMS Club"
         style="max-height:60px; width:auto; border:0;" /> --}}
</a>
{{-- <a href="{{ $url }}">
International MOMS Club</a> --}}
</td>
</tr>
