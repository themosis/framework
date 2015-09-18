<tr class="themosis-field-container">
    <th class="themosis-label" scope="row">
        {{ Themosis\Facades\Form::label($field['title'], ['for' => $field['id']]) }}
    </th>
    <td>
        {{ $field->metabox() }}
    </td>
</tr>