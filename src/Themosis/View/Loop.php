<?php

namespace Themosis\View;

class Loop
{
    /**
     * Get the id of the current post.
     *
     * @return int The ID of the current post.
     */
    public function id()
    {
        return get_the_ID();
    }

    /**
     * Get the title of the current post.
     *
     * @param int|\WP_post $post The post ID or \WP_Post object
     *
     * @return string The title of the current post.
     */
    public function title($post = null)
    {
        return get_the_title($post);
    }

    /**
     * Get the author of the current post.
     *
     * @return string The author of the current post.
     */
    public function author()
    {
        return get_the_author();
    }

    /**
     * Get author meta.
     *
     * @param string $field   User field name.
     * @param int    $user_id The user ID.
     *
     * @return string
     */
    public function authorMeta($field = '', $user_id = 0)
    {
        return get_the_author_meta($field, $user_id);
    }

    /**
     * Get the content of the current post.
     *
     * @param string $more_text    Content to show when there is more text.
     * @param bool   $strip_teaser Strip teaser content before the more text.
     *
     * @return string The content of the current post.
     */
    public function content($more_text = null, $strip_teaser = false)
    {
        $content = apply_filters('the_content', get_the_content($more_text, $strip_teaser));
        $content = str_replace(']]>', ']]&gt;', $content);

        return $content;
    }

    /**
     * Get the excerpt of the current post.
     *
     * @param int|\WP_Post $post
     *
     * @return string The excerpt of the current post.
     */
    public function excerpt($post = null)
    {
        return apply_filters('the_excerpt', get_the_excerpt($post));
    }

    /**
     * Get the post thumbnail of the current post.
     *
     * @param string|array $size The size of the current post thumbnail.
     * @param string|array $attr The attributes of the current post thumbnail.
     * @param int|\WP_Post $post The post ID or WP_Post object.
     *
     * @return string
     */
    public function thumbnail($size = 'post-thumbnail', $attr = '', $post = null)
    {
        if (is_null($post)) {
            $post = $this->id();
        }

        return get_the_post_thumbnail($post, $size, $attr);
    }

    /**
     * Get thumbnail url of current post.
     *
     * @param string|array $size The size of the current post thumbnail.
     * @param bool         $icon
     *
     * @return null|string
     */
    public function thumbnailUrl($size = null, $icon = false)
    {
        $data = wp_get_attachment_image_src(get_post_thumbnail_id($this->id()), $size, $icon);

        return (empty($data)) ? null : $data[0];
    }

    /**
     * Get the permalink of the current post.
     *
     * @param int|\WP_Post $post      The post ID or WP_Post object.
     * @param bool         $leavename Keep or not the post name.
     *
     * @return string The permalink of the current post.
     */
    public function link($post = 0, $leavename = false)
    {
        return get_permalink($post, $leavename);
    }

    /**
     * Get the categories of the current post.
     *
     * @param int $id The post ID.
     *
     * @return array The categories of the current post.
     */
    public function category($id = 0)
    {
        return get_the_category($id);
    }

    /**
     * Get the tags of the current post.
     *
     * @param int $id The post ID
     *
     * @return array The tags of the current post.
     */
    public function tags($id = 0)
    {
        $tags = get_the_tags($id);

        return $tags ? $tags : [];
    }

    /**
     * Get the terms (custom taxonomies) of the current post.
     *
     * @param string $taxonomy The custom taxonomy slug.
     * @param int|\WP_Post The post ID or WP_Post object
     * @param mixed $post
     *
     * @see https://codex.wordpress.org/Function_Reference/get_the_terms
     *
     * @return array|false|\WP_Error
     */
    public function terms($taxonomy, $post = 0)
    {
        if (! $post) {
            $post = $this->id();
        }

        $terms = get_the_terms($post, $taxonomy);

        return $terms ? $terms : [];
    }

    /**
     * Get the date of the current post.
     *
     * @param string       $d    Date format.
     * @param int|\WP_Post $post The post ID or WP_Post object
     *
     * @return string The date of the current post.
     */
    public function date($d = '', $post = null)
    {
        return get_the_date($d, $post);
    }

    /**
     * Add the classes for a given post.
     *
     * @author Guriev Eugen
     *
     * @param string|array $class   One or more classes to add to the post class list.
     * @param int|\WP_Post $post_id The post ID or the post object.
     *
     * @return string
     */
    public function postClass($class = '', $post_id = null)
    {
        return 'class="'.implode(' ', get_post_class($class, $post_id)).'"';
    }

    /**
     * Return the next link html anchor tag for post entries.
     *
     * @param string $label    Link content
     * @param int    $max_page Max pages in current query.
     *
     * @return string
     */
    public function nextPage($label = null, $max_page = 0)
    {
        return get_next_posts_link($label, $max_page);
    }

    /**
     * Return the previous link html anchor tag for post entries.
     *
     * @param string $label Link content
     *
     * @return string
     */
    public function previousPage($label = null)
    {
        return get_previous_posts_link($label);
    }

    /**
     * Return a pagination for any type of loops.
     *
     * @param array $args
     *
     * @see https://developer.wordpress.org/reference/functions/paginate_links/
     *
     * @return string|array
     */
    public function paginate($args = [])
    {
        return paginate_links($args);
    }
}
