{{ Themosis\Facades\Form::textarea($field['name'], $field['value'], array('class' => 'large-text', 'data-field' => 'textarea', 'id' => $field['id'], 'rows' => '5')) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif