{{ Themosis\Facades\Form::hidden($field['name'], $field['value'], array('id' => 'themosis-media-input', 'data-type' => $field['type'], 'data-size' => $field['size'], 'data-field' => 'media')) }}

<table class="themosis-media">
    <tr>
        <td class="themosis-media-preview <?php if(empty($field['value'])){ echo('themosis-media--hidden'); } ?>">
            <div class="themosis-media-preview-inner">
            @if(!empty($field['value']) && is_numeric($field['value']))
                @if(wp_attachment_is_image($field['value']))
                    {{ wp_get_attachment_image($field['value'], '_themosis_media', false, array('class' => 'themosis-media-thumbnail', 'alt' => 'Media Thumbnail')) }}
                @else
                    <img class="themosis-media-thumbnail" alt="Media Icon" src="{{ themosis_plugin_url(themosis_path('plugin')) }}/src/Themosis/_assets/images/themosisFileIcon.png"/>
                @endif
            @else
                <img class="themosis-media-thumbnail" alt="Media Icon" src="{{ themosis_plugin_url(themosis_path('plugin')) }}/src/Themosis/_assets/images/themosisFileIcon.png"/>
            @endif
            </div>
        </td>
        <td class="themosis-media-details">
            <div class="themosis-media-inner">
                <div class="themosis-media-infos <?php if(empty($field['value'])){ echo('themosis-media--hidden'); } ?>">
                    <h4><?php _e('Attachment ID'); ?>:</h4>
                    <p class="themosis-media__path">{{ $field['value'] }}</p>
                </div>
                <div class="themosis-media__buttons">
                    <button id="themosis-media-add" type="button" class="button button-primary <?php if(!empty($field['value'])){ echo('themosis-media--hidden'); } ?>"><?php _e('Add'); ?></button>
                    <button id="themosis-media-delete" type="button" class="button themosis-button-remove <?php if(empty($field['value'])){ echo('themosis-media--hidden'); } ?>"><?php  _e('Delete'); ?></button>
                </div>
            </div>
        </td>
    </tr>
</table>

@if(isset($field['info']))
    <div class="themosis-field-info">
        <p>{{ $field['info'] }}</p>
    </div>
@endif