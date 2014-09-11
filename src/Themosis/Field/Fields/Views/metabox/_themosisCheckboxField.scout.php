{{ Themosis\Facades\Form::checkbox($field['name'], $field['value'], array('id' => $field['id'], 'data-field' => 'checkbox')) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif