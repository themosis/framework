{{ Themosis\Facades\Form::select($field['name'], $field['options'], $field['value'], array('multiple' => $field['multiple'], 'data-field' => 'select', 'id' => $field['id'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif