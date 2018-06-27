<label for="{{ $__field->getAttribute('id') }}" {!! $__field->attributes($__field->getOptions('label_attr')) !!}>{{ $__field->getOptions('label') }}</label>
<textarea name="{{ $__field->getName() }}" {!! $__field->attributes($__field->getAttributes()) !!}>{{ $__field->getValue() }}</textarea>
@include($__field->getOptions('theme').'.types.includes.errors', ['field' => $__field])