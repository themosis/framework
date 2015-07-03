<tr class="themosis-field-container themosis-user-field-container">
    <th class="themosis-label themosis-user-label" scope="row">
        {{ Themosis\Facades\Form::label($field['id'], $field['title']) }}
    </th>
    <td>
        {{ $field->user() }}
    </td>
</tr>