{{ Themosis\Facades\Form::input('password', $field['name'], $field['value'], array('id' => $field['id'], 'class' => 'large-text', 'data-field' => 'password')) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif