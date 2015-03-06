{{ Themosis\Facades\Form::password($field['name'], $field['value'], array('id' => $field['id'], 'class' => $field['class'], 'data-field' => 'password')) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif