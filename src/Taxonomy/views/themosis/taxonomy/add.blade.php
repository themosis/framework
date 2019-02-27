@foreach($fields as $field)
    <div class="form-field {{ 'term-'.$field->getBaseName().'-wrap' }}">
        <label {!! $field->attributes($field->getOption('label_attr')) !!}>{{ $field->getOption('label') }}</label>
        {!! $field->render() !!}
    </div>
@endforeach