<label for="{{ $__field->getAttribute('id') }}">{{ $__field->getOptions('label') }}</label>
@php
    $name = $__field->getOptions('multiple') ? $__field->getName().'[]' : $__field->getName();
@endphp
<select name="{{ $name }}" id="{{ $__field->getAttribute('id') }}" {!! $__field->attributes($__field->getAttributes()) !!}>
    @foreach($field->getOptions('choices')->format()->get() as $group => $choices)
        @if(is_array($choices))
            <optgroup label="{{ $group }}">
                @foreach($choices as $label => $choice)
                    <option value="{{ $choice }}">{{ $label }}</option>
                @endforeach
            </optgroup>
        @else
            <option value="{{ $choices }}">{{ $group }}</option>
        @endif
    @endforeach
</select>