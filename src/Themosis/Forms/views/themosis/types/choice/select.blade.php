<label for="{{ $field->getAttribute('id') }}">{{ $field->getOptions('label') }}</label>
@php
    $name = $field->getOptions('multiple') ? $field->getName().'[]' : $field->getName();
@endphp
<select name="{{ $name }}" id="{{ $field->getAttribute('id') }}" {!! $field->attributes($field->getAttributes()) !!}>
    @foreach($field->getOptions('choices')->format()->get() as $group => $choices)
        @if(is_array($choices))
            <optgroup label="{{ $group }}">
                @foreach($choices as $label => $choice)
                    <?php
                        $selected = $field->selected(function ($option, $value) {
                            $values = (array) $value;

                            return ! is_null($value) && in_array($option, $values, true) ? 'selected' : '';
                        }, [$choice, $field->getValue()]);
                    ?>
                    <option value="{{ $choice }}" {{ $selected }}>{{ $label }}</option>
                @endforeach
            </optgroup>
        @else
            <?php
                $selected = $field->selected(function ($option, $value) {
                    $values = (array) $value;

                    return ! is_null($value) && in_array($option, $values, true) ? 'selected' : '';
                }, [$choices, $field->getValue()]);
            ?>
            <option value="{{ $choices }}" {{ $selected }}>{{ $group }}</option>
        @endif
    @endforeach
</select>