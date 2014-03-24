<?php
namespace Themosis\Taxonomy;

defined('DS') or die('No direct script access.');

/**
 * TaxFieldRenderer class
 *
 * Utility class that output html in order to display
 * the custom fields of the taxonomies.
 *
 * @since 1.0
 * @author Julien Lambé (julien@themosis.com)
 * @link http://www.themosis.com/
*/

class TaxFieldRenderer
{
    
    /**
     * Handle output for the "add term" page.
     *
     * @param string Type of the page where to output the field - Accepted values : add, edit.
     * @param array Fields datas.
     * @param object Term object given in the edit page. Optional parameter, default value null
     * @access public
    */
    public static function render($typeOfPage, $fields, $term = null)
    {        
        foreach ($fields as $field):
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Output the custom field
    	    /*-----------------------------------------------------------------------*/
            switch ($field['type']) {
                
                /*-----------------------------------------------------------------------*/
                // Text input field
                /*-----------------------------------------------------------------------*/
                case 'text':
                    
                    static::text($typeOfPage, $field, $term);
                    
                    break;
                
                /*-----------------------------------------------------------------------*/
                // Media custom field
                /*-----------------------------------------------------------------------*/
                case 'media':
                
                    static::media($typeOfPage, $field, $term);
                
                    break;
                default:
                    break;
            }
    	    
    	endforeach;
    }
    
    /**
     * Helper function that checks the page type.
     *
     * @param string Type of the page - 'add' or 'edit'
     * @return boolean
     * @access private
    */
    private static function isAddPage($typeOfPage)
    {
    	if ($typeOfPage === 'add') {
        	return true;
    	}
    	
    	return false;
    }
    
    /**
     * Open tags for the 'Add term' page.
     *
     * @param string The name attribute of the custom field
     * @param string The title property of the custom field
     * @return html
     * @access private
    */
    private static function openTagsForAddPage($name, $title)
    {
        ?>
        
    	<div class="form-field">
                <label for="<?php echo($name); ?>-field"><?php echo($title); ?></label>
                
        <?php
    }
    
    /**
     * Close tags for the 'Add term' page.
     *
     * @return html
     * @access private
    */
    private static function closeTagsForAddPage()
    {
    	?>
    	
    	</div>
    	
    	<?php
    }
    
    /**
     * Open tags for the 'Edit term' page.
     *
     * @param string The name attribute of the custom field.
     * @param string The title property of the custom field.
     * @return html
     * @access private   
    */
    private static function openTagsForEditPage($name, $title)
    {
    	?>
    	
    	<tr class="form-field">
    	        <th scope="row" valign="top">
    	            <label for="<?php echo($name); ?>-field"><?php echo($title); ?></label>
    	        </th>
    	        <td>
    	
    	<?php
    }
    
    /**
     * Close tags for the 'Edit term' page.
     *
     * @return html
     * @access private
    */
    private static function closeTagsForEditPage()
    {
    	?>
	        </td>
        </tr>
        
    	<?php
    }
    
    /**
     * Display the 'info' html tags.
     *
     * @param string Type of page 'edit' or 'add' term page.
     * @param string The info text to display
     * @return html
     * @access private
    */
    private static function infos($typeOfPage, $info)
    {
        /*-----------------------------------------------------------------------*/
        // ADD page description
        /*-----------------------------------------------------------------------*/
    	if (static::isAddPage($typeOfPage)) {
        	
            if (isset($info) && !empty($info)):
            
            ?>
            
                <p><?php echo(ucfirst($info)); ?></p>
                
            <?php
        
            endif;
            
        /*-----------------------------------------------------------------------*/
        // EDIT page description
        /*-----------------------------------------------------------------------*/	
    	} else {
        	
        	
            if (isset($info) && !empty($info)):
            
            ?>
        
                <span class="description"><?php echo(ucfirst($info)); ?></span>
            
            <?php
        
            endif;
        	
    	}
    }
    
    /**
     * Render a text field.
     *
     * @param string Which tags to output depending of the page.
     * @param array Field properties like name, value, ...
     * @param object The term object, stdObject given in the edit term page. (optional)
     * @access private
    */
    private static function text($typeOfPage, $field, $term = null)
    {
        extract($field);
        
        /*-----------------------------------------------------------------------*/
        // HTML output for the add term page.
        /*-----------------------------------------------------------------------*/
    	if (static::isAddPage($typeOfPage)):
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Open tags
    	    /*-----------------------------------------------------------------------*/
    	    static::openTagsForAddPage($name, $title);
    	    
            ?>
            
            <input type="text" id="<?php echo($name); ?>-field" name="<?php echo($taxonomy_slug.'['.$name.']'); ?>" value="" size="40"/>
            
            <?php
            
            /*-----------------------------------------------------------------------*/
            // Infos
            /*-----------------------------------------------------------------------*/
            static::infos($typeOfPage, $info);
            
            /*-----------------------------------------------------------------------*/
            // Close tags
            /*-----------------------------------------------------------------------*/
            static::closeTagsForAddPage();
        
        /*-----------------------------------------------------------------------*/
        // HTML output for the edit term page.
        /*-----------------------------------------------------------------------*/
    	else:
    	
    	    /*-----------------------------------------------------------------------*/
    	    // Retrieve the value for this field if it exists.
    	    /*-----------------------------------------------------------------------*/
    	    $optionkey = $taxonomy_slug.'_'.$term->term_id;
    	    
    	    // Array of values of all taxonomy fields.
    	    $values = get_option($optionkey);
    	    // Get the field value and check if it is set.
    	    $value = isset($values[$name]) ? $values[$name] : '';
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Open tags
    	    /*-----------------------------------------------------------------------*/
    	    static::openTagsForEditPage($name, $title);
    	    
    	    ?>
	            <input type="text" id="<?php echo($name); ?>-field" name="<?php echo($taxonomy_slug.'['.$name.']'); ?>" value="<?php if (isset($value) && !empty($value)) { echo($value); } ?>" size="40" />
	            <br />
	            
	            <?php
	            
	            /*-----------------------------------------------------------------------*/
	            // Infos
	            /*-----------------------------------------------------------------------*/
	            static::infos($typeOfPage, $info);
	            
    	    /*-----------------------------------------------------------------------*/
    	    // Close tags
    	    /*-----------------------------------------------------------------------*/
    	    static::closeTagsForEditPage();
    	    
    	endif;
    }
    
    /**
     * Render a Media field.
     *
     * @param string Which tags to output depending of the page.
     * @param array Field properties like name, value, ...
     * @param object The term object, stdObject given in the edit term page. (optional)
     * @access private
    */
    private static function media($typeOfPage, $field, $term = null)
    {
    	extract($field);
    	
    	/*-----------------------------------------------------------------------*/
        // HTML output for the add term page.
        /*-----------------------------------------------------------------------*/
    	if (static::isAddPage($typeOfPage)):
    	
    	    /*-----------------------------------------------------------------------*/
    	    // Open tags
    	    /*-----------------------------------------------------------------------*/
    	    static::openTagsForAddPage($name, $title);
    	
    	    ?>
    	    
    	    <table class="themosis-media-table">
					
				<tbody>
				
					<tr class="themosis-field-media">
					
						<td class="themosis-media-input">
						    <input type="text" size="40" name="<?php echo($taxonomy_slug.'['.$name.']'); ?>" id="<?php echo($name); ?>-field" />
						</td>
						
						<td>
						    <button type="button" class="button-primary themosis-media-button" id="themosis-media-add"><?php _e('Add', THEMOSIS_TEXTDOMAIN); ?></button>
						</td>
						
						<td>
						    <button type="button" class="button themosis-media-clear" id="themosis-media-clear"><?php _e('Clear', THEMOSIS_TEXTDOMAIN); ?></button>
						</td>
						
					</tr>
					
				</tbody>
				
			</table>
    	    
    	    <?php
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Infos
    	    /*-----------------------------------------------------------------------*/
    	    static::infos($typeOfPage, $info);
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Close tags
    	    /*-----------------------------------------------------------------------*/
    	    static::closeTagsForAddPage();
    	
    	/*-----------------------------------------------------------------------*/
        // HTML output for the edit term page.
        /*-----------------------------------------------------------------------*/
    	else:
    	
    	    /*-----------------------------------------------------------------------*/
    	    // Retrieve the value for this field if it exists.
    	    /*-----------------------------------------------------------------------*/
    	    $optionkey = $taxonomy_slug.'_'.$term->term_id;
    	    
    	    // Array of values of all taxonomy fields.
    	    $values = get_option($optionkey);
    	    // Get the field value and check if it is set.
    	    $value = isset($values[$name]) ? $values[$name] : '';
    	
    	    /*-----------------------------------------------------------------------*/
    	    // Open tags
    	    /*-----------------------------------------------------------------------*/
    	    static::openTagsForEditPage($name, $title);
    	    
    	    ?>
    	    
    	    <table class="themosis-media-table">
					
				<tbody>
				
					<tr class="themosis-field-media">
					
						<td class="themosis-media-input">
						    <input type="text" size="40" name="<?php echo($taxonomy_slug.'['.$name.']'); ?>" id="<?php echo($name); ?>-field" value="<?php if (isset($value) && !empty($value)) { echo($value); } ?>" />
						</td>
						
						<td>
						    <button type="button" class="button-primary themosis-media-button" id="themosis-media-add"><?php _e('Add', THEMOSIS_TEXTDOMAIN); ?></button>
						</td>
						
						<td>
						    <button type="button" class="button themosis-media-clear" id="themosis-media-clear"><?php _e('Clear', THEMOSIS_TEXTDOMAIN); ?></button>
						</td>
						
					</tr>
					
				</tbody>
				
			</table>
    	    
    	    <?php
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Infos
    	    /*-----------------------------------------------------------------------*/
    	    static::infos($typeOfPage, $info);
    	    
    	    /*-----------------------------------------------------------------------*/
    	    // Close tags
    	    /*-----------------------------------------------------------------------*/
    	    static::closeTagsForEditPage();
    	
    	endif;
    }
    
}

?>