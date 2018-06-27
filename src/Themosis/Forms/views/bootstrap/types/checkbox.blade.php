<div class="form-check">
    <input type="checkbox" name="{{ $__field->getName() }}" {!! $__field->attributes($__field->getAttributes()) !!}>
    <label {!! $__field->attributes($__field->getOptions('label_attr')) !!}>{!! $__field->getOptions('label') !!}</label>
</div>
@include($__field->getOptions('theme').'.types.includes.errors', ['field' => $__field])