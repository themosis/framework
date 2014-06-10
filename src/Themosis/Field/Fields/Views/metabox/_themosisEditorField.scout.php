<?php wp_editor($field['value'], $field['name'], $field['settings']); ?>

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif