@props(['name', 'value' => '', 'readonly' => false, 'oninput' => ''])
<div class="input-group">
    <span class="input-group-text">$</span>
    <input type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-control"
        @if($readonly) readonly @endif
        @if($oninput) oninput="{{ $oninput }}" @endif
        data-inputmask='"alias": "currency", "rightAlign": false, "groupSeparator": ",", "digits": 2, "digitsOptional": false, "placeholder": "0"'
        data-mask
        value="{{ $value ?? '' }}">
</div>
