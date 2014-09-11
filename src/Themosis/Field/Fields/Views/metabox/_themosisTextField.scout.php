{{ Themosis\Facades\Form::text($field['name'], $field['value'], array('id' => $field['id'], 'class' => 'large-text', 'data-field' => 'text')) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif