{{ Themosis\Facades\Form::textarea($field['name'], $field['value'], array_merge(array('class' => $field['class'], 'data-field' => 'textarea', 'id' => $field['id'], 'rows' => '5'), $field['attributes'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif