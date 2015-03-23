{{ Themosis\Facades\Form::date($field['name'], $field['value'], array_merge(array('id' => $field['id'], 'class' => $field['class'], 'data-field' => 'date'), $field['attributes'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif