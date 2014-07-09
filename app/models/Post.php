<?php

class Post {

    public static function all()
    {
        $query = new WP_Query(array('post_type' => 'post', 'posts_per_page' => -1));

        return $query->get_posts();
    }

} 