{{ Form::hidden($field['name'], $field['value'], array('id' => 'themosis-media-input', 'data-type' => $field['type'], 'data-size' => $field['size'], 'data-field' => 'media')) }}

<table class="themosis-media">
    <tr>
        <td class="themosis-media__buttons <?php if(!empty($field['value'])){ echo('themosis-media--hidden'); } ?>">
            <button id="themosis-media-add" type="button" class="button button-primary"><?php _e('Add', THEMOSIS_FRAMEWORK_TEXTDOMAIN); ?></button>
        </td>
        <td class="themosis-media__buttons <?php if(empty($field['value'])){ echo('themosis-media--hidden'); } ?>">
            <button id="themosis-media-delete" type="button" class="button"><?php  _e('Delete', THEMOSIS_FRAMEWORK_TEXTDOMAIN); ?></button>
        </td>
        <td <?php if(empty($field['value'])){ echo('class="themosis-media--hidden"'); } ?>>
            <p class="themosis-media__path">{{ $field['value'] }}</p>
        </td>
    </tr>
</table>

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif