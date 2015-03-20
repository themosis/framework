{{ Themosis\Facades\Form::date($field['name'], $field['value'], array('id' => $field['id'], 'class' => $field['class'], 'data-field' => 'date')) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif