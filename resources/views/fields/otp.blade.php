{{-- One box per digit; they submit as name[] and join server-side. --}}
@php
    $length = max(1, (int) $field->option('length', 6));
    $digits = str_split(str_pad((string) $value, $length, ' '));
@endphp
<div class="form-group">
    <label class="form-label">{{ $field->label }}</label>
    <div class="otp-grid" role="group" aria-label="{{ $field->label }}">
        @for ($i = 0; $i < $length; $i++)
            <input class="otp-input" type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                name="{{ $field->name }}[]"
                value="{{ trim($digits[$i] ?? '') }}"
                aria-label="{{ __('Digit :n of :total', ['n' => $i + 1, 'total' => $length]) }}"
                autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                @if ($i === 0) autofocus @endif
                @if ($field->isRequired()) required @endif>
        @endfor
    </div>
    @error($field->name)<div class="form-error">{{ $message }}</div>@enderror
    @if ($field->hint)<div class="form-help">{{ $field->hint }}</div>@endif
</div>
