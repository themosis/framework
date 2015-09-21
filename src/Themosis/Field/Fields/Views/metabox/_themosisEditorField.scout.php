<?php wp_editor($field['value'], $field['atts']['id'], $field['settings']); ?>

@if(isset($field['features']['info']))
    <div class="themosis-field-info">
        <p>{{ $field['features']['info'] }}</p>
    </div>
@endif