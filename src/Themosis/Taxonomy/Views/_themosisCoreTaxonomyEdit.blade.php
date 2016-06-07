<tr class="form-field {{ 'themosis-term-'.$field['name'].'-wrap' }}">
    <th scope="row">
        {!! Themosis\Facades\Form::label($field['features']['title'], ['for' => $field['atts']['id']]) !!}
    </th>
    <td>
        {!! $field->taxonomy() !!}
    </td>
</tr>