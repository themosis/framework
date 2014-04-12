<?php
namespace Themosis\PostType;

defined('DS') or die('No direct script access.');

class PostTypeData
{

	/**
	 * Default values for a custom post type
	*/
	private $defaults = array();

	/**
	 * Saved default values for the associated custom post type.
	 *
	 * @param string $name The custom post type plural name.
	*/
	public function __construct($name)
	{
		$labels = array(
		    'name' => __($name, THEMOSIS_TEXTDOMAIN),
		    'singular_name' => __($name, THEMOSIS_TEXTDOMAIN),
		    'add_new' => __('Add New', THEMOSIS_TEXTDOMAIN),
		    'add_new_item' => __('Add New '. $name, THEMOSIS_TEXTDOMAIN),
		    'edit_item' => __('Edit '. $name, THEMOSIS_TEXTDOMAIN),
		    'new_item' => __('New ' . $name, THEMOSIS_TEXTDOMAIN),
		    'all_items' => __('All ' . $name, THEMOSIS_TEXTDOMAIN),
		    'view_item' => __('View ' . $name, THEMOSIS_TEXTDOMAIN),
		    'search_items' => __('Search ' . $name, THEMOSIS_TEXTDOMAIN),
		    'not_found' =>  __('No '. $name .' found', THEMOSIS_TEXTDOMAIN),
		    'not_found_in_trash' => __('No '. $name .' found in Trash', THEMOSIS_TEXTDOMAIN),
		    'parent_item_colon' => '',
		    'menu_name' => __($name, THEMOSIS_TEXTDOMAIN)
	  	);

		$defaults = array(
			'label' 		=> __($name, THEMOSIS_TEXTDOMAIN),
			'labels' 		=> $labels,
			'description'	=> '',
			'public'		=> true,
			'menu_position'	=> 20,
			'has_archive'	=> true
		);

		$this->defaults = $defaults;
	}

	/**
	 * Allow the developer to override the default values
	 * by passing an array.
	 *
	 * @param array $params The custom post type arguments.
     * @return void
	 */
	public function set(array $params)
	{
		$this->defaults = array_merge($this->defaults, $params);
	}

	/**
	 * Retrieve the datas of the custom post type.
	 *
	 * @return object A Themosis\PostType\PostTypeData instance.
	 */
	public function get()
	{
		return $this;
	}


	/**
	 * Return the custom post type arguments.
	 *
	 * @access public
	 * @return array The custom post type arguments.
	 */
	public function getArgs()
	{
		return $this->defaults;
	}

	/**
	 * Parse the datas and check if the custom
	 * post type supports the editor. If not, load
	 * WP new uploader assets.
	 *
	 * @return bool True. False if editor not supported.
	 */
	public function handleEditor()
	{
		if (isset($this->defaults['supports'])) {

			if (!in_array('editor', $this->defaults['supports'])) {

				return false;

			}

		}

		return true;
	}

	/**
	 * Define a set of properties in order to make
	 * the custom post type restful.
     *
     * @todo Remove restful methods.
     *
     * @deprecated Not recommended.
     * @return void
	 */
	public function rest()
	{
		$rests = array(

			'public'				=> true,
			'exclude_from_search'	=> true,
			'publicly_queryable'	=> false,
			'show_in_nav_menus'		=> false,
			'show_in_admin_bar'		=> false,
			'has_archive'			=> false,
			'rewrite'				=> false

		);

		$this->defaults = array_merge($this->defaults, $rests);
	}
}

?>