<!-- Collection field -->
<div class="themosis-collection-wrapper">
    <script id="themosis-collection-item-template" type="text/template">
        <input type="hidden" name="{{ $field['name'] }}[]" value="<%= value %>"/>
        <div class="themosis-collection__item">
            <img src="<%= src %>" alt="Collection Item"/>
            <a class="check" title="Remove" href="#">
                <div class="media-modal-icon"></div>
            </a>
        </div>
    </script>
    <?php
        $show = empty($field['value']) ? '' : 'show';
    ?>
    <div class="themosis-collection-container {{ $show }}">
        <!-- Collection -->
        <div class="themosis-collection">
            <ul class="themosis-collection-list">
                @if (!empty($field['value']) && is_array($field['value']))
                    @foreach($field['value'] as $i => $item)
                        <li>
                            {{ Themosis\Facades\Form::hidden($field['name'].'[]', $item, array('id' => $field['id'].'-'.$i, 'data-field' => 'collection', 'data-limit' => 10)) }}
                            <div class="themosis-collection__item">
                                <?php
                                    $src = themosis_plugin_url(themosis_path('plugin')).'/src/Themosis/_assets/images/themosisFileIcon.png';

                                    if (wp_attachment_is_image($item))
                                    {
                                        $src = wp_get_attachment_image_src($item, '_themosis_media');
                                        $src = $src[0];
                                    }
                                ?>
                                <img src="{{ $src }}" alt="Collection Item"/>
                                <a class="check" title="Remove" href="#">
                                    <div class="media-modal-icon"></div>
                                </a>
                            </div>
                        </li>
                    @endforeach
                @endif
            </ul>
        </div>
        <!-- End collection -->
    </div>
    <div class="themosis-collection-buttons">
        <button id="themosis-collection-add" type="button" class="button button-primary"><?php _e('Add'); ?></button>
        <button id="themosis-collection-remove" type="button" class="button button-primary themosis-button-remove"><?php _e('Remove'); ?></button>
    </div>
    @if(isset($field['info']))
        <div class="themosis-field-info">
            <p>{{ $field['info'] }}</p>
        </div>
    @endif
</div>
<!-- End collection field -->