<tr class="themosis-field-container themosis-user-field-container">
    <th class="themosis-label themosis-user-label" scope="row">
        {!! Themosis\Facades\Form::label($field['features']['title'], ['for' => $field['atts']['id']]) !!}
    </th>
    <td>
        {!! $field->user() !!}
    </td>
</tr>