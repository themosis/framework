<input type="hidden" name="{{ $__field->getName() }}" {!! $__field->attributes($__field->getAttributes()) !!} value="{{ $__field->getValue() }}">
<input type="text" disabled="disabled" name="{{ $__field->getName() }}" class="all-options disabled" value="{{ $__field->getValue() }}">
