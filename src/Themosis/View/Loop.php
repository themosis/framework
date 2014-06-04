<?php
namespace Themosis\View;

class Loop
{
	/**
	 * Get the id of the current post.
	 * 
	 * @return int The ID of the current post.
	 */
	public static function id(){
		return get_the_ID();
	}

	/**
	 * Get the title of the current post.
	 * 
	 * @return string The title of the current post.
	 */
	public static function title(){
		return get_the_title();
	}

	/**
	 * Get the content of the current post.
	 *
	 * @return string The content of the current post.
	 */
	public static function content(){

		$content = apply_filters('the_content', get_the_content());
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
		
	}

	/**
	 * Get the excerpt of the current post.
	 *
	 * @return string The excerpt of the current post.
	 */
	public static function excerpt(){
		return get_the_excerpt();
	}

	/**
	 * Get the post thumbnail of the current post.
	 *
	 * @param string|array The size of the current post thumbnail.
	 * @param string|array The attributes of the current post thumbnail.
	 * @return string The thumbnail of the current post.
	 */
	public static function thumbnail($size = null, $attr = null){
		return get_the_post_thumbnail(static::id(), $size, $attr);
	}

	/**
	 * Get the permalink of the current post.
	 *
	 * @return string The permalink of the current post.
	 */
	public static function link(){
		return get_permalink();
	}

	/**
	 * Get the categories of the current post.
	 *
	 * @param int $id The post ID.
	 * @return array The categories of the current post.
	 */
	public static function category($id = null){
		return get_the_category($id);
	}

	/**
	 * Get the tags of the current post.
	 *
	 * @return array The tags of the current post.
	 */
	public static function tags(){
		return get_the_tags();
	}

	/**
	 * Get the terms (custom taxonomies) of the current post.
	 *
	 * @param string $taxonomy The custom taxonomy slug.
     * @see https://codex.wordpress.org/Function_Reference/get_the_terms
	 * @return array|false|WP_Error
	 */
	public static function terms($taxonomy){
		return get_the_terms(static::id(), $taxonomy);
	}
}