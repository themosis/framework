<label for="{{ $__field->getAttribute('id') }}">{{ $__field->getOptions('label') }}</label>
<input type="checkbox" name="{{ $__field->getName() }}" {!! $__field->attributes($__field->getAttributes()) !!}>
@include($__field->getOptions('theme').'.types.includes.errors', ['field' => $__field])