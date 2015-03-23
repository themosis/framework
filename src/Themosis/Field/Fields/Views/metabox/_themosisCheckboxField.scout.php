{{ Themosis\Facades\Form::checkbox($field['name'], $field['options'], $field['value'], array_merge(array('id' => $field['id'], 'data-field' => 'checkbox', 'class' => $field['class']),$field['attributes'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif