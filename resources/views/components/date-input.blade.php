@php
    $formatted = !empty($value) ? \Illuminate\Support\Carbon::parse($value)->format('m/d/Y') : '';
@endphp
<input type="text"
    name="{{ $name }}"
    id="{{ $name }}"
    class="form-control"
    data-inputmask='"alias": "datetime", "inputFormat": "mm/dd/yyyy"'
    data-mask
    value="{{ $formatted }}"
    placeholder="mm/dd/yyyy">
