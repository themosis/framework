{{ Themosis\Facades\Form::textarea($field['name'], $field['value'], $field['atts']) }}

@if(isset($field['features']['info']))
    <div class="themosis-field-info">
        <p>{{ $field['features']['info'] }}</p>
    </div>
@endif