@foreach($fields as $field)
    <div class="form-field {{ 'themosis-term-'.$field['name'].'-wrap' }}">
        {!! Themosis\Facades\Form::label($field['features']['title'], ['for' => $field['atts']['id']]) !!}
        {!! $field->taxonomy() !!}
    </div>
@endforeach