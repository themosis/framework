{{ Themosis\Facades\Form::radio($field['name'], $field['options'], $field['value'], array_merge(array('data-field' => 'radio', 'id' => $field['name'].'-id', 'class' => $field['class']), $field['attributes'])) }}

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif