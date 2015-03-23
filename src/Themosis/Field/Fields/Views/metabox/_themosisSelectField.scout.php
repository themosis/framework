{{ Themosis\Facades\Form::select($field['name'], $field['options'], $field['value'], array_merge(array('multiple' => $field['multiple'], 'data-field' => 'select', 'id' => $field['id'], 'class' => $field['class']), $field['attributes'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif