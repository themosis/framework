<?php
namespace Themosis\View;

defined('DS') or die('No direct script access.');

class Loop
{
	/**
	 * Get the id of the current post
	 * 
	 * @return string
	*/
	public static function id(){
		return get_the_ID();
	}

	/**
	 * Get the title of the current post
	 * 
	 * @return string
	*/
	public static function title(){
		return get_the_title();
	}

	/**
	 * Get the content of the current post
	 *
	 * @return string
	*/
	public static function content(){

		$content = apply_filters('the_content', get_the_content());
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
		
	}

	/**
	 * Get the excerpt of the current post
	 *
	 * @return string
	*/
	public static function excerpt(){
		return get_the_excerpt();
	}

	/**
	* Get the post thumbnail of the current post
	 *
	 * @param string|array
	 * @param string|array
	 * @return string
	*/
	public static function thumbnail($size = null, $attr = null){
		return get_the_post_thumbnail(static::id(), $size, $attr);
	}

	/**
	 * Get the permalink of the current post
	 *
	 * @return string
	*/
	public static function link(){
		return get_permalink();
	}

	/**
	 * Get the categories of the current post
	 *
	 * @param int (optional)
	 * @return array
	*/
	public static function category($id = null){
		return get_the_category($id);
	}

	/**
	 * Get the tags of the current post
	 *
	 * @return array
	*/
	public static function tags(){
		return get_the_tags();
	}

	/**
	 * Get the terms (custom taxonomies) of the current post
	 *
	 * @param string custom taxonomy slug
	 * @return mixed
	*/
	public static function terms($taxonomy){
		return get_the_terms(static::id(), $taxonomy);
	}
}