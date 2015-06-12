<?php // Hidden submit button early on so that the browser chooses the right button when form is submitted with Return key ?>
<div style="display:none;">
    <?php submit_button( __('Save'), 'button', 'save'); ?>
</div>
<div id="minor-publishing-actions">
    <div id="save-action">
        <?php if ( 'publish' != $post->post_status && 'future' != $post->post_status && 'pending' != $post->post_status ) { ?>
            <input <?php if ( 'private' == $post->post_status ) { ?>style="display:none"<?php } ?> type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save Draft'); ?>" class="button" />
        <?php } elseif ( 'pending' == $post->post_status && $can_publish ) { ?>
            <input type="submit" name="save" id="save-post" value="<?php esc_attr_e('Save as Pending'); ?>" class="button" />
        <?php } ?>
        <span class="spinner"></span>
    </div>
    <?php if ( $post_type_object->public ) : ?>
        <div id="preview-action">
            <?php
            if ( 'publish' == $post->post_status ) {
                $preview_link = esc_url( get_permalink( $post->ID ) );
                $preview_button = __( 'Preview Changes' );
            } else {
                $preview_link = set_url_scheme( get_permalink( $post->ID ) );

                /**
                 * Filter the URI of a post preview in the post submit box.
                 *
                 * @since 2.0.5
                 * @since 4.0.0 $post parameter was added.
                 *
                 * @param string  $preview_link URI the user will be directed to for a post preview.
                 * @param WP_Post $post         Post object.
                 */
                $preview_link = esc_url( apply_filters( 'preview_post_link', add_query_arg( 'preview', 'true', $preview_link ), $post ) );
                $preview_button = __( 'Preview' );
            }
            ?>
            <a class="preview button" href="<?php echo $preview_link; ?>" target="wp-preview-<?php echo (int) $post->ID; ?>" id="post-preview"><?php echo $preview_button; ?></a>
            <input type="hidden" name="wp-preview" id="wp-preview" value="" />
        </div>
    <?php endif; // public post type ?>
    <div class="clear"></div>
</div><!-- #minor-publishing-actions -->

<div id="misc-publishing-actions">

    <div class="misc-pub-section misc-pub-post-status"><label for="post_status"><?php _e('Status:') ?></label>
<span id="post-status-display">
<?php
switch ( $post->post_status ) {
    case 'private':
        _e('Privately Published');
        break;
    case 'publish':
        _e('Published');
        break;
    case 'future':
        _e('Scheduled');
        break;
    case 'pending':
        _e('Pending Review');
        break;
    case 'draft':
    case 'auto-draft':
        _e('Draft');
        break;
}
?>
</span>
        <?php if ( 'publish' == $post->post_status || 'private' == $post->post_status || $can_publish ) { ?>
            <a href="#post_status" <?php if ( 'private' == $post->post_status ) { ?>style="display:none;" <?php } ?>class="edit-post-status hide-if-no-js"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit status' ); ?></span></a>

            <div id="post-status-select" class="hide-if-js">
                <input type="hidden" name="hidden_post_status" id="hidden_post_status" value="<?php echo esc_attr( ('auto-draft' == $post->post_status ) ? 'draft' : $post->post_status); ?>" />
                <select name='post_status' id='post_status'>
                    <?php if ( 'publish' == $post->post_status ) : ?>
                        <option<?php selected( $post->post_status, 'publish' ); ?> value='publish'><?php _e('Published') ?></option>
                    <?php elseif ( 'private' == $post->post_status ) : ?>
                        <option<?php selected( $post->post_status, 'private' ); ?> value='publish'><?php _e('Privately Published') ?></option>
                    <?php elseif ( 'future' == $post->post_status ) : ?>
                        <option<?php selected( $post->post_status, 'future' ); ?> value='future'><?php _e('Scheduled') ?></option>
                    <?php endif; ?>
                    <option<?php selected( $post->post_status, 'pending' ); ?> value='pending'><?php _e('Pending Review') ?></option>
                    <?php if ( 'auto-draft' == $post->post_status ) : ?>
                        <option<?php selected( $post->post_status, 'auto-draft' ); ?> value='draft'><?php _e('Draft') ?></option>
                    <?php else : ?>
                        <option<?php selected( $post->post_status, 'draft' ); ?> value='draft'><?php _e('Draft') ?></option>
                    <?php endif; ?>
                </select>
                <a href="#post_status" class="save-post-status hide-if-no-js button"><?php _e('OK'); ?></a>
                <a href="#post_status" class="cancel-post-status hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
            </div>

        <?php } ?>
    </div><!-- .misc-pub-section -->

    <div class="misc-pub-section misc-pub-visibility" id="visibility">
        <?php _e('Visibility:'); ?> <span id="post-visibility-display"><?php

            if ( 'private' == $post->post_status ) {
                $post->post_password = '';
                $visibility = 'private';
                $visibility_trans = __('Private');
            } elseif ( !empty( $post->post_password ) ) {
                $visibility = 'password';
                $visibility_trans = __('Password protected');
            } elseif ( $post_type == 'post' && is_sticky( $post->ID ) ) {
                $visibility = 'public';
                $visibility_trans = __('Public, Sticky');
            } else {
                $visibility = 'public';
                $visibility_trans = __('Public');
            }

            echo esc_html( $visibility_trans ); ?></span>
        <?php if ( $can_publish ) { ?>
            <a href="#visibility" class="edit-visibility hide-if-no-js"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit visibility' ); ?></span></a>

            <div id="post-visibility-select" class="hide-if-js">
                <input type="hidden" name="hidden_post_password" id="hidden-post-password" value="<?php echo esc_attr($post->post_password); ?>" />
                <?php if ($post_type == 'post'): ?>
                    <input type="checkbox" style="display:none" name="hidden_post_sticky" id="hidden-post-sticky" value="sticky" <?php checked(is_sticky($post->ID)); ?> />
                <?php endif; ?>
                <input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="<?php echo esc_attr( $visibility ); ?>" />
                <input type="radio" name="visibility" id="visibility-radio-public" value="public" <?php checked( $visibility, 'public' ); ?> /> <label for="visibility-radio-public" class="selectit"><?php _e('Public'); ?></label><br />
                <?php if ( $post_type == 'post' && current_user_can( 'edit_others_posts' ) ) : ?>
                    <span id="sticky-span"><input id="sticky" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky( $post->ID ) ); ?> /> <label for="sticky" class="selectit"><?php _e( 'Stick this post to the front page' ); ?></label><br /></span>
                <?php endif; ?>
                <input type="radio" name="visibility" id="visibility-radio-password" value="password" <?php checked( $visibility, 'password' ); ?> /> <label for="visibility-radio-password" class="selectit"><?php _e('Password protected'); ?></label><br />
                <span id="password-span"><label for="post_password"><?php _e('Password:'); ?></label> <input type="text" name="post_password" id="post_password" value="<?php echo esc_attr($post->post_password); ?>"  maxlength="20" /><br /></span>
                <input type="radio" name="visibility" id="visibility-radio-private" value="private" <?php checked( $visibility, 'private' ); ?> /> <label for="visibility-radio-private" class="selectit"><?php _e('Private'); ?></label><br />

                <p>
                    <a href="#visibility" class="save-post-visibility hide-if-no-js button"><?php _e('OK'); ?></a>
                    <a href="#visibility" class="cancel-post-visibility hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
                </p>
            </div>
        <?php } ?>

    </div><!-- .misc-pub-section -->

    <?php
    /* translators: Publish box date format, see http://php.net/date */
    $datef = __( 'M j, Y @ G:i' );
    if ( 0 != $post->ID ) {
        if ( 'future' == $post->post_status ) { // scheduled for publishing at a future date
            $stamp = __('Scheduled for: <b>%1$s</b>');
        } else if ( 'publish' == $post->post_status || 'private' == $post->post_status ) { // already published
            $stamp = __('Published on: <b>%1$s</b>');
        } else if ( '0000-00-00 00:00:00' == $post->post_date_gmt ) { // draft, 1 or more saves, no date specified
            $stamp = __('Publish <b>immediately</b>');
        } else if ( time() < strtotime( $post->post_date_gmt . ' +0000' ) ) { // draft, 1 or more saves, future date specified
            $stamp = __('Schedule for: <b>%1$s</b>');
        } else { // draft, 1 or more saves, date specified
            $stamp = __('Publish on: <b>%1$s</b>');
        }
        $date = date_i18n( $datef, strtotime( $post->post_date ) );
    } else { // draft (no saves, and thus no date specified)
        $stamp = __('Publish <b>immediately</b>');
        $date = date_i18n( $datef, strtotime( current_time('mysql') ) );
    }

    if ( ! empty( $args['args']['revisions_count'] ) ) :
        $revisions_to_keep = wp_revisions_to_keep( $post );
        ?>
        <div class="misc-pub-section misc-pub-revisions">
            <?php
            if ( $revisions_to_keep > 0 && $revisions_to_keep <= $args['args']['revisions_count'] ) {
                echo '<span title="' . esc_attr( sprintf( __( 'Your site is configured to keep only the last %s revisions.' ),
                        number_format_i18n( $revisions_to_keep ) ) ) . '">';
                printf( __( 'Revisions: %s' ), '<b>' . number_format_i18n( $args['args']['revisions_count'] ) . '+</b>' );
                echo '</span>';
            } else {
                printf( __( 'Revisions: %s' ), '<b>' . number_format_i18n( $args['args']['revisions_count'] ) . '</b>' );
            }
            ?>
            <a class="hide-if-no-js" href="<?php echo esc_url( get_edit_post_link( $args['args']['revision_id'] ) ); ?>"><span aria-hidden="true"><?php _ex( 'Browse', 'revisions' ); ?></span> <span class="screen-reader-text"><?php _e( 'Browse revisions' ); ?></span></a>
        </div>
    <?php endif;

    if ( $can_publish ) : // Contributors don't get to choose the date of publish ?>
        <div class="misc-pub-section curtime misc-pub-curtime">
        <span id="timestamp">
	<?php printf($stamp, $date); ?></span>
        <a href="#edit_timestamp" class="edit-timestamp hide-if-no-js"><span aria-hidden="true"><?php _e( 'Edit' ); ?></span> <span class="screen-reader-text"><?php _e( 'Edit date and time' ); ?></span></a>
        <div id="timestampdiv" class="hide-if-js"><?php touch_time(($action == 'edit'), 1); ?></div>
        </div><?php // /misc-pub-section ?>
    <?php endif; ?>

    <?php
    /**
     * Fires after the post time/date setting in the Publish meta box.
     *
     * @since 2.9.0
     */
    do_action( 'post_submitbox_misc_actions' );
    ?>
</div>
<div class="clear"></div>