<?php
namespace Themosis\Taxonomy;

/**
 * TaxFieldRenderer class.
 *
 * Utility class that output html in order to display
 * the custom fields of the taxonomies.
 */
class TaxFieldRenderer
{
    /**
     * Handle output for the "add term" page.
     *
     * @param string    $typeOfPage Type of the page where to output the field - Accepted values : 'add', 'edit'.
     * @param array     $fields     The fields to output.
     * @param \stdClass $term       The term object sent by WordPress.
     */
    public static function render($typeOfPage, array $fields, \stdClass $term = null)
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
     * @param string $typeOfPage Type of the page: 'add' or 'edit'.
     *
     * @return bool True. False if not the 'add' page.
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
     * @param string $name  The name attribute of the custom field.
     * @param string $title The title property of the custom field.
     */
    private static function openTagsForAddPage($name, $title)
    {
        ?>
        
    	<div class="form-field">
                <label for="<?php echo $name;
        ?>-field"><?php echo $title;
        ?></label>
                
        <?php

    }

    /**
     * Close tags for the 'Add term' page.
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
     * @param string $name  The name attribute of the custom field.
     * @param string $title The title property of the custom field.
     */
    private static function openTagsForEditPage($name, $title)
    {
        ?>
    	
    	<tr class="form-field">
    	        <th scope="row" valign="top">
    	            <label for="<?php echo $name;
        ?>-field"><?php echo $title;
        ?></label>
    	        </th>
    	        <td>
    	
    	<?php

    }

    /**
     * Close tags for the 'Edit term' page.
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
     * @param string $typeOfPage Type of page: 'edit' or 'add'.
     * @param string $info       The info text to display.
     */
    private static function infos($typeOfPage, $info)
    {
        /*-----------------------------------------------------------------------*/
        // ADD page description
        /*-----------------------------------------------------------------------*/
        if (static::isAddPage($typeOfPage)) {
            if (isset($info) && !empty($info)):

            ?>
            
                <p><?php echo ucfirst($info);
            ?></p>
                
            <?php

            endif;

        /*-----------------------------------------------------------------------*/
        // EDIT page description
        /*-----------------------------------------------------------------------*/
        } else {
            if (isset($info) && !empty($info)):

            ?>
        
                <span class="description"><?php echo ucfirst($info);
            ?></span>
            
            <?php

            endif;
        }
    }

    /**
     * Render a text field.
     *
     * @param string    $typeOfPage Which page is viewed: 'add' or 'edit'.
     * @param array     $field      The field properties.
     * @param \stdClass $term       The term object sent by WordPress.
     */
    private static function text($typeOfPage, array $field, \stdClass $term = null)
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
            
            <input type="text" id="<?php echo $name;
        ?>-field" name="<?php echo $taxonomy_slug.'['.$name.']';
        ?>" value="" size="40"/>
            
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
	            <input type="text" id="<?php echo $name;
        ?>-field" name="<?php echo $taxonomy_slug.'['.$name.']';
        ?>" value="<?php if (isset($value) && !empty($value)) {
    echo $value;
}
        ?>" size="40" />
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
     * @param string    $typeOfPage Which page is viewed: 'add' or 'edit'.
     * @param array     $field      The field properties.
     * @param \stdClass $term       The term object sent by WordPress.
     */
    private static function media($typeOfPage, array $field, \stdClass $term = null)
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
						    <input type="text" size="40" name="<?php echo $taxonomy_slug.'['.$name.']';
        ?>" id="<?php echo $name;
        ?>-field" />
						</td>
						
						<td>
						    <button type="button" class="button-primary themosis-media-button" id="themosis-media-add"><?php _e('Add', THEMOSIS_FRAMEWORK_TEXTDOMAIN);
        ?></button>
						</td>
						
						<td>
						    <button type="button" class="button themosis-media-clear" id="themosis-media-clear"><?php _e('Clear', THEMOSIS_FRAMEWORK_TEXTDOMAIN);
        ?></button>
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
						    <input type="text" size="40" name="<?php echo $taxonomy_slug.'['.$name.']';
        ?>" id="<?php echo $name;
        ?>-field" value="<?php if (isset($value) && !empty($value)) {
    echo $value;
}
        ?>" />
						</td>
						
						<td>
						    <button type="button" class="button-primary themosis-media-button" id="themosis-media-add"><?php _e('Add', THEMOSIS_FRAMEWORK_TEXTDOMAIN);
        ?></button>
						</td>
						
						<td>
						    <button type="button" class="button themosis-media-clear" id="themosis-media-clear"><?php _e('Clear', THEMOSIS_FRAMEWORK_TEXTDOMAIN);
        ?></button>
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
