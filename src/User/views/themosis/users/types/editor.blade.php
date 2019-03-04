@if(function_exists('wp_editor'))
    @php(wp_editor($__field->getRawValue(), $__field->getName(), $__field->getOption('settings')))
@endif
@include($__field->getTheme().'.types.includes.info', ['field' => $__field])