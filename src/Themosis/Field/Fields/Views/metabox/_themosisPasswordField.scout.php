{{ Themosis\Facades\Form::password($field['name'], $field['value'], array_merge(array('id' => $field['id'], 'class' => $field['class'], 'data-field' => 'password'), $field['attributes'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif