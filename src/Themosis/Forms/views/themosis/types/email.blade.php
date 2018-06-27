<label for="{{ $__field->getAttribute('id') }}">{{ $__field->getOptions('label') }}</label>
<input type="email" name="{{ $__field->getName() }}" {!! $__field->attributes($__field->getAttributes()) !!} value="{{ $__field->getValue() }}">
@include($__field->getOptions('theme').'.types.includes.errors', ['field' => $__field])