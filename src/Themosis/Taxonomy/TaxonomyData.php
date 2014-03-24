<?php
namespace Themosis\Taxonomy;

defined('DS') or die('No direct script access.');

class TaxonomyData
{
	/**
	 * Default values for a taxonomy.
	*/
	private $defaults = array();	

	/**
	 * Saved default values for the associated taxonomy.
	 * 
	 * @param string
	*/
	public function __construct($name)
	{
		$labels = array(
		    'name' => _x($name, THEMOSIS_TEXTDOMAIN),
		    'singular_name' => _x($name, THEMOSIS_TEXTDOMAIN),
		    'search_items' =>  __( 'Search ' . $name, THEMOSIS_TEXTDOMAIN),
		    'all_items' => __( 'All ' . $name, THEMOSIS_TEXTDOMAIN),
		    'parent_item' => __( 'Parent ' . $name ,THEMOSIS_TEXTDOMAIN),
		    'parent_item_colon' => __( 'Parent ' . $name . ': ' ,THEMOSIS_TEXTDOMAIN),
		    'edit_item' => __( 'Edit ' . $name ,THEMOSIS_TEXTDOMAIN), 
		    'update_item' => __( 'Update ' . $name ,THEMOSIS_TEXTDOMAIN),
		    'add_new_item' => __( 'Add New ' . $name ,THEMOSIS_TEXTDOMAIN),
		    'new_item_name' => __( 'New '. $name .' Name' ,THEMOSIS_TEXTDOMAIN),
		    'menu_name' => __($name ,THEMOSIS_TEXTDOMAIN)
	  	);

		$defaults = array(
			'label' 		=> __($name, THEMOSIS_TEXTDOMAIN),
			'labels' 		=> $labels,
			'public'		=> true,
			'query_var'		=> true
		);

		$this->defaults = $defaults;
	}

	/**
	 * Allow the developer to override the default values
	 * by passing an array.
	 * 
	 * @param array
	*/
	public function set($params)
	{
		$this->defaults = array_merge($this->defaults, $params);
	}

	/**
	 * Retrieve the saved values of the taxonomy
	 * 
	 * @return array
	*/
	public function get()
	{
		return $this->defaults;
	}
}