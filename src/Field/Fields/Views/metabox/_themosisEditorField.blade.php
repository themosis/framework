<?php wp_editor($field['value'], $field['atts']['id'], $field['settings']); ?>

@if(isset($field['features']['info']))
    <div class="themosis-field-info">
        <p class="description">{!! $field['features']['info'] !!}</p>
    </div>
@endif