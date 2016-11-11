{!! Themosis\Facades\Form::hidden($field['name'], $field['value'], $field['atts']) !!}

<table class="themosis-media">
    <tr>
        <td class="themosis-media-preview <?php if(empty($field['value'])){ echo('themosis-media--hidden'); } ?>">
            <div class="themosis-media-preview-inner">
                <?php
                    $isFile = false;
                    $src = '';

                    if (!empty($field['value']) && is_numeric($field['value']))
                    {
                        if (wp_attachment_is_image($field['value']))
                        {
                            $src = wp_get_attachment_image_src($field['value'], '_themosis_media');
                            $src = $src[0];
                        }
                        else
                        {
                            $src = wp_get_attachment_image_src($field['value'], '_themosis_media', true);
                            $src = $src[0];
                            $isFile = true;
                        }
                    }
                ?>
                <div class="centered">
                    <img class="themosis-media-thumbnail <?php if ($isFile){ echo('icon'); } ?>" alt="Media Thumbnail" src="{{ $src }}"/>
                </div>
                <div class="filename <?php if ($isFile){ echo('show'); } ?>">
                    <div><?php if(!empty($field['value']) && is_numeric($field['value'])){ echo(get_the_title($field['value'])); } ?></div>
                </div>
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

@if(isset($field['features']['info']))
    <div class="themosis-field-info">
        <p class="description">{!! $field['features']['info'] !!}</p>
    </div>
@endif