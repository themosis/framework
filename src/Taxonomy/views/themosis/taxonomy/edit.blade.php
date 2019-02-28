@foreach($fields as $field)
    <tr class="form-field {{ 'term-'.$field->getBaseName().'-wrap' }}">
        <th scope="row">
            <label {!! $field->attributes($field->getOption('label_attr')) !!}>{{ $field->getOption('label') }}</label>
        </th>
        <td>
            {!! $field->render() !!}
        </td>
    </tr>
@endforeach