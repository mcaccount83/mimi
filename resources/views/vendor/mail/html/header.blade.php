<tr>
<td class="header">
    <a href="{{ $url }}" style="display:inline-block;">
        {{-- Must use app.url (absolute) not base_url (relative) - email clients cannot resolve relative paths --}}
        <img src="{{ rtrim(config('app.url'), '/') }}/images/logo-long2.png" alt="International MOMS Club" width="250">
    </a>
{{-- <a href="{{ $url }}">
International MOMS Club</a> --}}
</td>
</tr>
