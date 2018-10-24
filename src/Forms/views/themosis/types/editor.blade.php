<label {!! $__field->attributes($__field->getOption('label_attr')) !!}>{!! $__field->getOption('label') !!}</label>
@if(function_exists('wp_editor'))
    @php(wp_editor($__field->getValue(), $__field->getName(), $__field->getOption('settings')))
@endif
@include($__field->getTheme().'.types.includes.info', ['field' => $__field])
@include($__field->getTheme().'.types.includes.errors', ['field' => $__field])
