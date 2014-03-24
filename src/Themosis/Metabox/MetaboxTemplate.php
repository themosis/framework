<?php
namespace Themosis\Metabox;

use stdClass;
use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

abstract class MetaboxTemplate
{
    /**
	 * Add the field saved value and properties for display
	 *
	 * @param int
	 * @param array
	 * @return array
	*/
	protected static function populate($id, $datas)
	{
		$newDatas = array();

		foreach ($datas['args'] as $field) {

			// Define the 'title' property
			$field['title'] = static::setTitle($field);

			// Define the 'info' property
			$field['info'] = static::setInfo($field);

			// Define the 'value' property
			$value = get_post_meta($id, $field['name'], true);
			$field['value'] = $value;

			$newDatas[] = $field;
		}

		return $newDatas;
	}

	/**
	 * Set the 'title' property for the custom field.
	 * Add a default value if the 'title' does not exist.
	 *
	 * @param array (the custom field)
	 * @return string
	*/
	private static function setTitle($field)
	{
		// If not exists, set the title equal to the name property
		if (!isset($field['title'])) {
			$field['title'] = $field['name'];
		}

		return ucfirst(trim($field['title']));
	}

	/**
	 * Set the 'info' property for the custom field
	 * Return an emplty string if there is no value.
	 *
	 * @param array (the custom field)
	 * @return string
	*/
	private static function setInfo($field)
	{
		// If not exists, set the title equal to the name property
		if (!isset($field['info'])) {
			$field['info'] = '';
		}

		return ucfirst(trim($field['info']));
	}

	/**
	 * Output the <input type="text" /> tag template.
	 *
	 * @param array
	*/
	protected static function text($field)
	{
		?>

			<tr class="themosis-field-container">

				<th class="themosis-label" scope="row">
					<label for="<?php echo($field['name']); ?>-id"><?php echo($field['title']); ?> :</label>
				</th>
				<td>
					<input type="text" name="<?php echo($field['name']); ?>" data-type="text" class="large-text" id="<?php echo($field['name']); ?>-id" value="<?php echo($field['value']); ?>" />
					<?php if ($field['info']) : ?>
						<div class="themosis-field-info">
							<p><?php echo($field['info']); ?></p>
						</div>
					<?php endif; ?>
				</td>

			</tr>

		<?php
	}

	/**
	 * Output the <textarea> tag template
	 *
	 * @param array
	*/
	protected static function textarea($field)
	{
		?>

			<tr class="themosis-field-container">

				<th class="themosis-label" scope="row">
					<label for="<?php echo($field['name']); ?>-id"><?php echo($field['title']); ?> :</label>
				</th>
				<td>
					<textarea class="large-text" name="<?php echo($field['name']); ?>" data-type="textarea" id="<?php echo($field['name']); ?>-id" rows="5"><?php echo($field['value']); ?></textarea>
					<?php if ($field['info']) : ?>
						<div class="themosis-field-info">
							<p><?php echo($field['info']); ?></p>
						</div>
					<?php endif; ?>
				</td>

			</tr>

		<?php
	}

	/**
	 * Output a single <input type="checkbox" /> tag template
	 *
	 * @param array
	*/
	protected static function checkbox($field)
	{
		?>
			<tr class="themosis-field-container">

				<th class="themosis-label" scope="row">
					<label for="<?php echo($field['name']); ?>-id"><?php echo($field['title']); ?> :</label>
				</th>
				<td>
					<input type="checkbox" name="<?php echo($field['name']); ?>" data-type="checkbox" id="<?php echo($field['name']); ?>-id" <?php if ($field['value'] === 'on') { echo('checked="checked"'); } ?>>
					<?php if ($field['info']) : ?>
						<div class="themosis-field-info">
							<p><?php echo($field['info']); ?></p>
						</div>
					<?php endif; ?>
				</td>

			</tr>
		<?php
	}

	/**
	 * Output multiple <input type="checkbox" /> tags template
	 *
	 * @param array
	*/
	protected static function checkboxes($field)
	{

		extract($field);

		?>
			<tr class="themosis-field-container">

				<th class="themosis-label" scope="row">
					<label for="<?php echo($name); ?>-id"><?php echo($title); ?> :</label>
				</th>
				<td>
					<?php
					for($i = 0; $i < count($options); $i++):
					?>
						<label class="label">
							<input type="checkbox" name="<?php echo($name); ?>[]" data-type="checkboxes" id="<?php echo($name.'-id-'.$i); ?>" value="<?php echo($options[$i]); ?>" <?php if (is_array($value) && in_array($options[$i], $value)) { echo('checked="checked"'); } ?> />
							<span class="title"><?php echo(ucfirst($options[$i])); ?></span>
						</label><br />
					<?php
					endfor;
					?>
					<?php if ($info) : ?>
						<div class="themosis-field-info">
							<p><?php echo($info); ?></p>
						</div>
					<?php endif; ?>
				</td>

			</tr>
		<?php
	}

	/**
	 * Output <input type="radio" /> tags template
	 *
	 * @param array
	*/
	protected static function radio($field)
	{

		extract($field);

		?>
			<tr class="themosis-field-container">

				<th class="themosis-label" scope="row">
					<label for="<?php echo($name.'-id'); ?>"><?php echo($title); ?> :</label>
				</th>
				<td>
					<?php
					for ($i = 0; $i < count($options); $i++) :
					?>
						<label class="label">
							<input type="radio" name="<?php echo($name); ?>[]" data-type="radio" id="<?php echo($name.'-id-'.$i); ?>" value="<?php echo($options[$i]); ?>" <?php if (is_array($value) && in_array($options[$i], $value)) { echo('checked="checked"'); } ?> />
							<span class="title"><?php echo(ucfirst($options[$i])); ?></span>
						</label><br />
					<?php
					endfor;
					?>
					<?php if ($info) : ?>
						<div class="themosis-field-info">
							<p><?php echo($info); ?></p>
						</div>
					<?php endif; ?>
				</td>

			</tr>
		<?php
	}

	/**
	 * Output <select /> tag template
	 *
	 * @param array
	*/
	protected static function select($field)
	{

		extract($field);

		?>
			<tr class="themosis-field-container">

				<th class="themosis-label" scope="row">

					<label for="<?php echo($name); ?>-id"><?php echo($title); ?> :</label>

				</th>
				<td>
					<select name="<?php echo($name); if ($multiple) { echo('[]'); } ?>" id="<?php echo($name); ?>-id" <?php if ($multiple) { echo('multiple="multiple"'); } ?>>

						<?php

    						foreach ($options as $key => $option) :

        						/**
        						 * If $key is numeric, apply the $key for the <option> value attribute.
        						 * Else if $key is an array, apply the subarray keys as value attribute.
        						 * WARNING : the subarray MUST be an associative array.
        						 * This structure is made in order to give flexibility to the developers so
        						 * they can define their own values for the <select> tag.
        						*/
                                // If $key is a string, we define a <optgroup> tag.
        						if (is_string($key)):

                                ?>

    								<optgroup label="<?php echo(ucfirst($key)); ?>">

                                    <?php

                                        // Use this array in order to define the selected <option> tag
                                        $mergedOptions = array();

                                        foreach($options as $groupName => $groupValues):

                                            foreach($groupValues as $k => $val):

                                                if (is_string($k)):

                                                    // Merge the options in one array for associative array
                                                    $mergedOptions[$k] = $val;

                                                else:

                                                    // Merge the options in one array for indexed array
                                                    array_push($mergedOptions, $val);

                                                endif;

                                            endforeach;

                                        endforeach;

                                        // Display the <option> tags
                                        foreach($option as $subKey => $subValue):

                                            // If the $subKey is a string, then we use an associative array
                                            // with custom values as keys.
                                            if(is_string($subKey)):

                                                // Value to add at the value attribute of the <option> tag
                                                $subKey = trim($subKey);

                                                if (is_array($value)):
                                                ?>
                                                    <option value="<?php echo($subKey); ?>" <?php if (in_array($subKey, $value)) { echo('selected="selected"'); } ?>><?php echo(ucfirst($subValue)); ?></option>
                                                <?php

                                                else:

                                                ?>
                                                    <option value="<?php echo($subKey); ?>" <?php if (!empty($value) && $value === $subKey) { echo('selected="selected"'); } ?>><?php echo(ucfirst($subValue)); ?></option>
                                                <?php

                                                endif;

                                            else:

                                                // Real value to add to the value attribute of the <option> tag
                                                $tagValue = array_search($subValue, $mergedOptions);

                                                if (is_array($value)):

                                                    // Return true or false - Help display the selected <option> tag
                                                    $selected = (in_array($tagValue, $value) && !empty($value)) ? true : false;
                                                ?>
                                                    <option value="<?php echo($tagValue); ?>" <?php if ($selected) { echo('selected="selected"'); } ?>><?php echo(ucfirst($subValue)); ?></option>
                                                <?php

                                                else:

                                            ?>
                                                    <option value="<?php echo($tagValue); ?>" <?php if (!empty($value) && $mergedOptions[$value] === $subValue){ echo('selected="selected"'); } ?>><?php echo(ucfirst($subValue)); ?></option>
                                            <?php
                                                endif;

                                            endif;

                                        endforeach;

                                    ?>

    								</optgroup>

    						<?php
    						    // No <optgroup> tag
    							else :

    							    /**
        							 * We can pass either an array with key/value pair in order to define our own
        							 * <option> value attribute or we use the given array of strings.
    							    */
    							    if (is_array($option)) :

    							        foreach ($option as $subKey => $subValue) :

    							            if (is_array($value)) :
    							            ?>
    							                <option value="<?php echo($subKey); ?>" <?php if (in_array($subKey, $value)) { echo('selected="selected"'); } ?>><?php echo(ucfirst($subValue)); ?></option>
    							            <?php

    							            else:

                                            ?>
            									<option value="<?php echo($subKey); ?>" <?php if ($value == $subKey) { echo('selected="selected"'); } ?>><?php echo(ucfirst($subValue)); ?></option>
                                        <?php
    									    endif;

        								endforeach;

                                    elseif (is_string($option)) :

                                        if (is_array($value)):
                                    ?>
                                            <option value="<?php echo($key); ?>" <?php if (in_array($key, $value)) { echo('selected="selected"'); } ?>><?php echo(ucfirst($option)); ?></option>
                                    <?php

                                        else:

                                    ?>
        									<option value="<?php echo($key); ?>" <?php if ((int)$value === $key){ echo('selected="selected"'); } ?>><?php echo(ucfirst($option)); ?></option>
                                    <?php
                                        endif;

                                    endif;

    							endif;

    						endforeach;
						?>

					</select>

					<?php if ($info) : ?>
						<div class="themosis-field-info">
							<p><?php echo($info); ?></p>
						</div>
					<?php endif; ?>
				</td>

			</tr>
		<?php
	}

	/**
	 * Output media template
	 *
	 * @param array
	*/
	protected static function media($field)
	{

	    extract($field);

		?>
			<tr class="themosis-field-container themosis-field-media">

				<th class="themosis-label" scope="row">

					<label for="<?php echo($name); ?>-id"><?php echo($title); ?> :</label>

				</th>
				<td>

					<table class="themosis-media-table">

						<tbody>

							<tr>

								<td class="themosis-media-input">
								    <input type="text" name="<?php echo($name); ?>" data-type="text" class="large-text" id="<?php echo($name); ?>-id" value="<?php if (isset($value) && !empty($value)) { echo($value); } ?>" />
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

					<?php if ($info) : ?>
						<div class="themosis-field-info">
							<p><?php echo($info); ?></p>
						</div>
					<?php endif; ?>

				</td>

			</tr>
		<?php
	}

	/**
	 * Output the 'infinite' template
	 *
	 * @param array
	*/
	protected static function infinite($field)
	{
	    extract($field);
		?>
			<tr class="themosis-field-container themosis-field-infinite">
				<th class="themosis-label" scope="row">
					<label for="<?php echo($name); ?>-id"><?php echo($title); ?> :</label>
				</th>
				<td>
					<div class="themosis-infinite-container">
						<table class="themosis-infinite" id="themosis-infinite-sortable">
							<tbody>
							    <?php
                                // Check the row count and display the rows in consequence.
                                $rows = (is_array($value) && !empty($value)) ? count($value) : 1;

                                for ($i = 1; $i <= $rows; $i++) :
                                ?>
                                    <!-- Inner Rows - What is repeated -->
                                    <tr class="themosis-infinite-row">
                                        <td class="themosis-infinite-order">
                        					<span><?php echo($i); ?></span>
                        				</td>
                        				<td class="themosis-infinite-inner">
                        					<table>
                        					    <tbody>
                            						<!-- Fields - Custom fields for each row -->
                            						<?php
                            						// Display the custom fields of each row
                                				    foreach ($fields as $f) :

                                				        $rowIndex = 'row'.$i;

                                                        // Add a 'value' key to the $f field
                                                        if (isset($value) && !empty($value) && isset($value[$rowIndex]['fields'][$f['name']])) {
                                                            $f['value'] = $value[$rowIndex]['fields'][$f['name']];
                                                        } else {
                                                            $f['value'] = '';
                                                        }

                                                        // Set the 'title' key
                                				        $f['title'] = static::setTitle($f);

                                				        // Set the 'info' key
                                				        $f['info'] = static::setInfo($f);

                                                        // Rename each "name" attribute for the fields
                                				        $f['name'] = $name.'[row'.$i.'][fields]['.$f['name'].']';

                                				        // Check 'options' key for checkboxes - radio - select field
                                				        if (in_array($f['type'], array('checkboxes', 'radio', 'select'))) {
                                    				        $f['options'] = (isset($f['options'])) ? $f['options'] : array();

                                    				        if ($f['type'] === 'select') {
                                        				        $f['multiple'] = (isset($f['multiple'])) ? $f['multiple'] : false;
                                    				        }
                                				        }

                                				        // Check 'fields' key for infinite
                                				        if ($f['type'] === 'infinite') {
                                    				        $f['fields'] = (isset($f['fields'])) ? $f['fields'] : array();
                                				        }

                                				        $signature = trim($f['type']);
                                				        // Output the field
                                				        static::$signature($f);

                                				    endforeach;
                            						?>
                            						<!-- End Fields -->
                        						</tbody>
                        					</table>
                        				</td>
                        				<td class="themosis-infinite-options">
                        					<span class="themosis-infinite-add"></span>
                        					<span class="themosis-infinite-remove"></span>
                        				</td>
                                    </tr>
                                    <!-- End Inner Rows -->
                                <?php
                                endfor;
							    ?>
							</tbody>
						</table>
						<div class="themosis-infinite-add-field-container">
							<button type="button" id="themosis-infinite-main-add" class="button-primary"><?php _e('Add row', THEMOSIS_TEXTDOMAIN); ?></button>
						</div>
					</div>
				</td>
			</tr>
		<?php
	}


	/**
	 * Output the editor custom field.
	 *
	 * @access protected
	 * @static
	 * @param array The custom field.
	 * @return void
	 */
	protected static function editor($field)
	{
        ?>

			<tr class="themosis-field-container">

				<th class="themosis-label" scope="row">
					<label for="<?php echo($field['name']); ?>-id"><?php echo($field['title']); ?> :</label>
				</th>
				<td class="themosis-wp-editor">
					<?php
						wp_editor($field['value'], $field['name'], $field['editor_args']);
					?>
					<?php if ($field['info']) : ?>
						<div class="themosis-field-info">
							<p><?php echo($field['info']); ?></p>
						</div>
					<?php endif; ?>
				</td>

			</tr>

		<?php
	}

}
?>