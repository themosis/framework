{!! Themosis\Facades\Form::select($field['name'], $field['options'], $field['value'], $field['atts']) !!}

@if(isset($field['features']['info']))
    <div class="themosis-field-info">
        <p class="description">{!! $field['features']['info'] !!}</p>
    </div>
@endif