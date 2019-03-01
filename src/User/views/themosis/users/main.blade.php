@if(! empty($section->getTitle()))
    <h2>{{ $section->getTitle() }}</h2>
@endif
<table class="form-table">
    <tbody>
        @foreach($fields as $field)
            <tr>
                <th scope="row">
                    <label {!! $field->attributes($field->getOption('label_attr')) !!}>{{ $field->getOption('label') }}</label>
                </th>
                <td>
                    {!! $field->render() !!}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>