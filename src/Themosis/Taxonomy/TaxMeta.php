<?php
namespace Themosis\Taxonomy;

defined('DS') or die('No direct script access.');

/**
 * TaxMeta class
 * 
 * Allow the user to retrieve a custom field of a taxonomy.
 *
 * @since 1.0
 * @author Julien Lambé (julien@themosis.com)
 * @link http://www.themosis.com/
*/

class TaxMeta
{    
    /**
     * Retrieve all custom fields of a term.
     *
     * @param string The taxonomy slug used when you registered it.
     * @param int The slug of the custom field also used at registration.
     * @return array | boolean
     * @access public
    */
    public static function all($taxonomySlug, $term_id)
    {
    	$key = $taxonomySlug.'_'.$term_id;
    	
    	return get_option($key);
    }
    
    /**
     * Retrieve one custom field of a term.
     *
     * @param string The taxonomy slug.
     * @param int The term ID.
     * @param string The name key corresponding to the field.
     * @return mixed The saved value in the option table. Could be string or arrays.
     * @access public
    */
    public static function get($taxonomySlug, $term_id, $key)
    {
    	$values = static::all($taxonomySlug, $term_id);
    	
    	return $values[$key];
    }
    
}

?>