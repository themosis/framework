{{ Themosis\Facades\Form::checkbox($field['name'], $field['options'], $field['value'], array('id' => $field['id'], 'data-field' => 'checkbox', 'class' => $field['class'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif