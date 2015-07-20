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
	 * @return string The title of the current post.
	 */
	public function title()
	{
		return get_the_title();
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
	 * Get the content of the current post.
	 *
	 * @return string The content of the current post.
	 */
	public function content()
	{
		$content = apply_filters('the_content', get_the_content());
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}

	/**
	 * Get the excerpt of the current post.
	 *
	 * @return string The excerpt of the current post.
	 */
	public function excerpt()
	{
		return get_the_excerpt();
	}

	/**
	 * Get the post thumbnail of the current post.
	 *
	 * @param string|array The size of the current post thumbnail.
	 * @param string|array The attributes of the current post thumbnail.
	 * @return string The thumbnail of the current post.
	 */
	public function thumbnail($size = null, $attr = null)
	{
		return get_the_post_thumbnail($this->id(), $size, $attr);
	}
	
	/**
	 * Get thumbnail url of current post.
	 *
	 * @param string|array $size The size of the current post thumbnail.
	 * @param bool $icon
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
	 * @return string The permalink of the current post.
	 */
	public function link()
	{
		return get_permalink();
	}

	/**
	 * Get the categories of the current post.
	 *
	 * @param int $id The post ID.
	 * @return array The categories of the current post.
	 */
	public function category($id = null)
	{
		return get_the_category($id);
	}

	/**
	 * Get the tags of the current post.
	 *
	 * @return array The tags of the current post.
	 */
	public function tags()
	{
		return get_the_tags();
	}

	/**
	 * Get the terms (custom taxonomies) of the current post.
	 *
	 * @param string $taxonomy The custom taxonomy slug.
     * @see https://codex.wordpress.org/Function_Reference/get_the_terms
	 * @return array|false|\WP_Error
	 */
	public function terms($taxonomy)
	{
		return get_the_terms($this->id(), $taxonomy);
	}
	
	/**
	 * Get the date of the current post.
	 *
	 * @param string $d Date format.
	 * @return string The date of the current post.
	 */
	public function date($d = '')
	{
		return get_the_date($d);
	}

	/**
	 * Add the classes for a given post.
	 *
	 * @author Guriev Eugen
	 * @param string|array $class One or more classes to add to the post class list.
	 * @param int|\WP_Post $post_id The post ID or the post object.
	 * @return string
	 */
	public function postClass($class = '', $post_id = null)
	{
		return 'class="'.join(' ', get_post_class($class, $post_id)).'"';
	}
}
