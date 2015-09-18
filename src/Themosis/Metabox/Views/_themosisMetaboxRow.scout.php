<tr class="themosis-field-container">
    <th class="themosis-label" scope="row">
        {{ Themosis\Facades\Form::label($field['features']['title'], ['for' => $field['atts']['id']]) }}
    </th>
    <td>
        {{ $field->metabox() }}
    </td>
</tr>