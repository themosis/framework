<?php

namespace Themosis\Route\Bindings;

use WP_Post;

class NullableWpPost
{
    public function __construct(
        public ?int $ID = null,
        public int $post_author = 0,
        public string $post_date = '0000-00-00 00:00:00',
        public string $post_date_gmt = '0000-00-00 00:00:00',
        public string $content = '',
        public string $post_title = '',
        public string $post_excerpt = '',
        public string $post_status = 'publish',
        public string $comment_status = 'open',
        public string $ping_status = 'open',
        public string $post_password = '',
        public string $post_name = '',
        public string $to_ping = '',
        public string $pinged = '',
        public string $post_modified = '0000-00-00 00:00:00',
        public string $post_modified_gmt = '0000-00-00 00:00:00',
        public string $post_content_filtered = '',
        public int $post_parent = 0,
        public string $guid = '',
        public int $menu_order = 0,
        public string $post_type = 'post',
        public string $post_mime_type = '',
        public int $comment_count = 0,
    ) {
    }

    public function toWpPost(): WP_Post
    {
        return new WP_Post($this);
    }
}
