<tr class="themosis-field-container">
    <th class="themosis-label" scope="row">
        {{ Themosis\Facades\Form::label($field['id'], $field['title']) }}
    </th>
    <td>
        {{ $field->metabox() }}
    </td>
</tr>