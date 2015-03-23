{{ Themosis\Facades\Form::checkboxes($field['name'], $field['options'], $field['value'], array_merge(array('data-field' => 'checkbox', 'id' => $field['name'].'-id', 'class' => $field['class']),$field['attributes'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif