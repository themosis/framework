<?php
global $action;

$post_type = $__post->post_type;
$post_type_object = get_post_type_object($post_type);
$can_publish = current_user_can($post_type_object->cap->publish_posts);

?>
<div class="submitbox" id="submitpost">
    <div id="minor-publishing">
        <!-- Later -->
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
                if(current_user_can('delete_post', $__post->ID))
                {
                    if (!EMPTY_TRASH_DAYS)
                    {
                        $delete_text = __('Delete Permanently');
                    }
                    else
                    {
                        $delete_text = __('Move to Trash');
                    }
            ?>
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
            if (!in_array($__post->post_status, array_keys($statuses)) || 0 == $__post->ID)
            {
                // Check if user as "publish_posts" capability
                if ($can_publish)
                {
                    // Check if post date is longer than now.
                    // If so, the post has to be scheduled.
                    if (!empty($__post->post_date_gmt) && time() < strtotime($__post->post_date_gmt . ' +0000'))
                    {
                ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Schedule') ?>"/>
                        <?php submit_button(__('Schedule'), 'primary button-large', 'publish', false, ['accesskey' => 'p']); ?>
                <?php
                    }
                    else
                    {
                    // The user can publish the post.
                ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Publish') ?>"/>
                        <?php submit_button(__('Publish'), 'primary button-large', 'publish', false, ['accesskey' => 'p']); ?>
                <?php
                    }
                }
                else
                {
                    // User can't publish a post. So he can only submit it for review.
                    ?>
                    <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Submit for Review') ?>"/>
                    <?php submit_button(__('Submit for Review'), 'primary button-large', 'publish', false, ['accesskey' => 'p']); ?>
                <?php
                }
            }
            else
            {
                // Current status of the post is in the list of registered statuses.
                // So, show the "update" button
            ?>
                <input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e('Update') ?>" />
                <input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php esc_attr_e('Update') ?>" />
            <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>
</div>