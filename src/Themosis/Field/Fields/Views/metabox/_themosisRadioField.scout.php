{{ Themosis\Facades\Form::radio($field['name'], $field['options'], $field['value'], array('data-field' => 'radio', 'id' => $field['name'].'-id')) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif