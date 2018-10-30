<?php
global $action;

$post_type = $__post->post_type;
$post_type_object = get_post_type_object($post_type);
$can_publish = current_user_can($post_type_object->cap->publish_posts);

/**
 * Registered statuses.
 */
$status_keys = array_keys($statuses);

?>
<div class="submitbox" id="submitpost">
    <div id="minor-publishing">
        <?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key?>
        <div style="display:none;">
            <?php submit_button(__('Save'), 'button', 'save'); ?>
        </div>

        <div id="minor-publishing-actions">
            <div id="save-action">
                <?php
                // Save draft buttons.
                // If current status is not equal to "publish", "future" or "pending", then a "Save Draft" button is available.
                switch ($__post->post_status) {
                    case 'pending':
                        // If status is "pending"
                        if ($can_publish) {
                            ?>
                            <input type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save as Pending'); ?>" class="button" />
                            <?php
                        }
                        break;
                    default:
                        // If status not one defined, then we can save a draft version.
                        if (! in_array($__post->post_status, $status_keys)) {
                            ?>
                        <input <?php if ('private' == $__post->post_status) {
                                ?>style="display:none"<?php
                            } ?> type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save Draft'); ?>" class="button" />
                        <?php
                        }
                        break;
                }
                ?>
                <span class="spinner"></span>
            </div>
            <?php
            // Preview buttons.
            // If the custom post type is "public", show the "preview" button.
            if ($post_type_object->public) {
                ?>
                <div id="preview-action">
                    <?php
                        // If current status is one of registered status
                        // then set preview button text to "Preview Changes".
                        if (in_array($__post->post_status, $status_keys)) {
                            $preview_link = esc_url(get_permalink($__post->ID));
                            $preview_button = __('Preview Changes');
                        } else {
                            // Set preview button text to "Preview" only.
                            $preview_link = set_url_scheme(get_permalink($__post->ID));

                            /**
                             * Filter the URI of a post preview in the post submit box.
                             *
                             * @since 2.0.5
                             * @since 4.0.0 $post parameter was added.
                             *
                             * @param string  $preview_link URI the user will be directed to for a post preview.
                             * @param WP_Post $post         Post object.
                             */
                            $preview_link = esc_url(apply_filters('preview_post_link', add_query_arg('preview', 'true', $preview_link), $__post));
                            $preview_button = __('Preview');
                        } ?>
                    <a class="preview button" href="{{ $preview_link }}" target="wp-preview-{{ (int) $__post->ID }}" id="post-preview">{{ $preview_button }}</a>
                    <input type="hidden" name="wp-preview" id="wp-preview" value="" />
                </div>
            <?php
            }
            ?>
            <div class="clear"></div>
        </div><!-- #minor-publishing-actions -->

        <div id="misc-publishing-actions">
            <!-- Post status -->
            <div class="misc-pub-section misc-pub-post-status">
                <label for="post_status"><?php _e('Status:'); ?></label>
                <span id="post-status-display">
                    <?php
                    // By default, a "auto-draft" status is set.
                    // Check if current status is in the registered list.
                    if (in_array($__post->post_status, $status_keys)) {
                        $s = $statuses[$__post->post_status];
                        _e($s['label']);
                    } else {
                        // If not in the list (is auto-draft), then display "Draft" status only.
                        _e('Draft');
                    }
                    ?>
                </span>
                <?php
                // Edit statuses...
                if (in_array($__post->post_status, $status_keys) || 'private' == $__post->post_status || $can_publish) {
                    ?>
                    <a href="#post_status" <?php if ('private' == $__post->post_status) {
                        ?>style="display:none;" <?php
                    } ?>class="edit-post-status hide-if-no-js"><span aria-hidden="true"><?php _e('Edit'); ?></span> <span class="screen-reader-text"><?php _e('Edit status'); ?></span></a>

                    <div id="post-status-select" class="hide-if-js">
                        <input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr(('auto-draft' === $__post->post_status) ? 'draft' : $__post->post_status); ?>" />
                        <?php
                            $choices = [
                                __('Draft') => 'draft'
                            ];

                    foreach ($statuses as $key => $status) {
                        $choices[$status['label']] = $key;
                    }

                    $select =  Field::choice('post_status', [
                                'attributes' => [
                                    'id' => 'post_status'
                                ],
                                'choices' => $choices,
                                'data' => $__post->post_status,
                                'theme' => 'themosis'
                            ]);
                    $select->setPrefix(''); ?>
                        {!! $select->render(); !!}
                        <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e('OK'); ?></a>
                        <a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
                    </div>

                <?php
                } ?>
            </div><!-- .misc-pub-section -->
            <!-- End post status -->
        </div>
        <?php
        /**
         * Fires after the post time/date setting in the Publish meta box.
         *
         * @since 2.9.0
         */
        do_action('post_submitbox_misc_actions');
        ?>
        <div class="clear"></div>
    </div>

    <div id="major-publishing-actions">
        <?php
            /**
             * Fires at the beginning of the publishing actions section of the Publish meta box.
             *
             * @since 2.7.0
             */
            do_action('post_submitbox_start');
        ?>
        <div id="delete-action">
            <?php
                if (current_user_can('delete_post', $__post->ID)) {
                    if (! EMPTY_TRASH_DAYS) {
                        $delete_text = __('Delete Permanently');
                    } else {
                        $delete_text = __('Move to Trash');
                    } ?>
                <a class="submitdelete deletion" href="{{ get_delete_post_link($__post->ID) }}">{{ $delete_text }}</a>
            <?php
                }
            ?>
        </div>

        <div id="publishing-action">
            <span class="spinner"></span>
            <?php
            // If current post status is not in the list of registered statuses,
            // it might a new one, schedule one or one to submit for review (based on default WordPres posts publish metabox).
            if (! in_array($__post->post_status, $status_keys) || 0 == $__post->ID) {
                // Check if user as "publish_posts" capability
                if ($can_publish) {
                    // Check if post date is longer than now.
                    // If so, the post has to be scheduled.
                    if (! empty($__post->post_date_gmt) && time() < strtotime($__post->post_date_gmt.' +0000')) {
                        ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Schedule'); ?>"/>
                        <?php submit_button(__('Schedule'), 'primary button-large', 'publish', false, ['accesskey' => 'p']); ?>
                <?php
                    } else {
                        // The user can publish the post.
                        // This case mean the post is a new one with default status of draft.
                        // By default, use the "publish_text" property of the first registered custom status.?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e($statuses[$status_keys[0]]['publish_text']); ?>"/>
                        <?php submit_button($statuses[$status_keys[0]]['publish_text'], 'primary button-large', 'publish', false, ['accesskey' => 'p']); ?>
                <?php
                    }
                } else {
                    // User can't publish a post. So he can only submit it for review.?>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review'); ?>"/>
                    <?php submit_button(__('Submit for Review'), 'primary button-large', 'publish', false, ['accesskey' => 'p']); ?>
                <?php
                }
            } else {
                // Current status of the post is in the list of registered statuses.
                // So, show the "update" button?>
                <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update'); ?>" />
                <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e('Update'); ?>" />
            <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>
</div>