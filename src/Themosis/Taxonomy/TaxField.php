<?php
namespace Themosis\Taxonomy;

defined('DS') or die('No direct script access.');

/**
 * TaxField class
 * 
 * Allow the user to add custom fields to a taxonomy.
 *
 * @since 1.0
 * @author Julien Lambé (julien@themosis.com)
 * @link http://www.themosis.com/
*/

class TaxField
{   
    /**
     * The taxonomy slug.
     *
     * @var string
     * @access private
    */
    private $slug;
    
    /**
     * Tell if the taxonomy exists.
     *
     * @var boolean
     * @access private
    */
    private $exists = false;
    
    /**
     * The custom fields of the taxonomy.
     *
     * @var array
     * @access private
    */
    private $fields = array();

    /**
     * Class constructor.
     *
     * @param string The taxonomy slug used by action hooks.
     * @access private
    */
    private function __construct($taxonomySlug)
    {   
        $this->slug = $taxonomySlug;
        
        /*-----------------------------------------------------------------------*/
        // Check if the taxonomy exists before going further.
        /*-----------------------------------------------------------------------*/
    	add_action('wp_loaded', array(&$this, 'check'));
    }
    
    /**
     * Init the custom taxonomy field.
     *
     * @param string The taxonomy slug.
     * @return object An instance of TaxField. Allow for chaining methods.
     * @access public
    */
    public static function make($taxonomySlug)
    {   
        $slug = trim($taxonomySlug);
        
        if (!empty($slug) && is_string($slug)) {
        
            return new static($slug);
            
        } else {
            
            throw new TaxonomyException("String expected as a parameter.");
            
        }
    }
    
    /**
     * Check if the taxonomy exists. Call by the action hook 'wp_loaded'.
     * If not, throw an exception.
     *
     * @access public
    */
    public function check()
    {
    	if (taxonomy_exists($this->slug)) {
        	
        	/*-----------------------------------------------------------------------*/
        	// Set the exists property to true.
        	// Allow the user to define the fields later with the "set" method.
        	/*-----------------------------------------------------------------------*/
        	$this->exists = true;
        	
    	} else {
        	
        	throw new TaxonomyException('The taxonomy slug "'.$this->slug.'" does not exists.');
        	
    	}
    }
    
    /**
     * Set the custom fields for the taxonomy.
     *
     * @param array Array of arrays. Use the Field class to add custom field.
     * @return object Return the instance for chaining.
     * @access public
    */
    public function set($fields)
    {        
        if (is_array($fields) && !empty($fields)) {
            
            /*-----------------------------------------------------------------------*/
        	// Parse the fields and save them to the instance property.
        	/*-----------------------------------------------------------------------*/
        	$this->fields = $this->parse($fields, $this->slug);
        	
        	/*-----------------------------------------------------------------------*/
            // Add the field to the "add term page"
            // {$taxonomy_slug}_add_form_fields
            /*-----------------------------------------------------------------------*/
            $slug = $this->slug.'_add_form_fields';
            
            add_action($slug, array(&$this, 'addFields'));
            
            /*-----------------------------------------------------------------------*/
            // Add the field to the "edit term page"
            /*-----------------------------------------------------------------------*/
            $slug = $this->slug.'_edit_form_fields';
            
            add_action($slug, array(&$this, 'editFields'));
            
            /*-----------------------------------------------------------------------*/
            // Register the save hooks on the add + edit pages.
            /*-----------------------------------------------------------------------*/
            add_action('edited_'.$this->slug, array(&$this, 'save'), 10, 2);
            add_action('create_'.$this->slug, array(&$this, 'save'), 10, 2);
            
            /*-----------------------------------------------------------------------*/
            // Register the delete hook in order to remove the custom fields
            // from the options table.
            /*-----------------------------------------------------------------------*/
            add_action('delete_term', array($this,'delete'), 10,2);
            
            return $this;
        	
        } else {
            
            throw new TaxonomyException('Array expected as a parameter.');
            
        }
    }
    
    /**
     * Display the custom fields on the add terms page.
     *
     * @access public
    */
    public function addFields()
    {
    	/*-----------------------------------------------------------------------*/
    	// Output the custom fields
    	/*-----------------------------------------------------------------------*/
    	TaxFieldRenderer::render('add', $this->fields);
    }
    
    /**
     * Display the custom fields on the edit term page.
     *
     * @param object The term object stdObject
     * @access public
    */
    public function editFields($term)
    {
    	/*-----------------------------------------------------------------------*/
    	// Output the custom fields
    	/*-----------------------------------------------------------------------*/
    	TaxFieldRenderer::render('edit', $this->fields, $term);
    }
    
    /**
     * Save the fields values in the options table.
     *
     * @param int The term ID
     * @access public
    */
    public function save($term_id)
    {    	
    	if (isset($_POST[$this->slug])) {
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Option unique key
    	    /*-----------------------------------------------------------------------*/
    	    $optionKey = $this->slug.'_'.$term_id;
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Retrieve an existing value if it exists...
    	    /*-----------------------------------------------------------------------*/
    		$term_meta = get_option($optionKey);
    		
    		/*-----------------------------------------------------------------------*/
    		// Get all fields names's key
    		/*-----------------------------------------------------------------------*/
    		$cat_keys = array_keys($_POST[$this->slug]);
    		
    		foreach ($cat_keys as $key) {
    		
    			if (isset($_POST[$this->slug][$key])) {
    			
    				$term_meta[$key] = $_POST[$this->slug][$key];
    				
    			}
    			
    		}
    		
    		/*-----------------------------------------------------------------------*/
    		// Save the fields
    		/*-----------------------------------------------------------------------*/
    		update_option($optionKey, $term_meta );
    	}
    }
    
    /**
     * Delete the fields from the database.
     *
     * @param object The term object - stdObject
     * @param int The term ID
     * @access public
    */
    public function delete($term, $term_id)
    {
        $key = $this->slug.'_'.$term_id;
        
    	delete_option($key);
    }
    
    /**
     * Parse the fields and mix them with a default one.
     *
     * @param array The registered fields.
     * @param string The taxonomy slug.
     * @return array The parsed array of fields with a TaxonomySlug key/value.
     * @access private
    */
    private function parse($fields, $taxonomySlug)
	{
		$newFields = array();

		foreach ($fields as $field) {
			
			$defaults = array(
				'name'              => 'default_field',
				'title'             => ucfirst($field['name']),
				'info'              => '',
				'default'           => '',
				'type'              => 'text',
				'options'           => array(),
				'class'             => '',
				'multiple'	        => false,
				'fields'	        => array(),
				'taxonomy_slug'     => $taxonomySlug
			);

			/*-----------------------------------------------------------------------*/
			// Mix values from defaults and $args and then extract
			// the results as $variables
			/*-----------------------------------------------------------------------*/
			extract(wp_parse_args($field, $defaults));

			$field_args = array(
				'type'              => $type,
				'name'              => $name,
				'info'              => $info,
				'default'           => $default,
				'options'           => $options,
				'label_for'         => $name,
				'class'             => $class,
				'title'		        => $title,
				'multiple'	        => $multiple,
				'fields'	        => $fields,
				'taxonomy_slug'     => $taxonomy_slug
			);

			/*-----------------------------------------------------------------------*/
			// Add new settings
			/*-----------------------------------------------------------------------*/
			$newFields[] = $field_args;

		}

		return $newFields;
	}
    
}

?>