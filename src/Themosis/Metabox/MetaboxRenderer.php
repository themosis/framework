<?php
namespace Themosis\Metabox;

use Themosis\Configuration\Application;
use Themosis\Session\Session;

defined('DS') or die('No direct script access.');

class MetaboxRenderer extends MetaboxTemplate
{
	/**
	 * Display the metabox content. Only drawing the custom fields with their
	 * associated value(s) if available.
	 *
	 * @param object
	 * @param array
	*/
	public static function render($post, $datas)
	{
		// Add nonce fields
		wp_nonce_field(Session::nonceAction, Session::nonceName);

		// Populate the datas with their saved values and check optional properties.
		$datas['args'] = static::populate($post->ID, $datas);

		echo('<table class="form-table themosis-metabox" data-id="'.$datas['id'].'">');

			echo('<tbody>');

				/**
				 * Loop through each custom fields and display
				 * the appropriate template.
				*/
				foreach ($datas['args'] as $customField) {

					/**
					 * Display the template by type, defined using the Field class
					 * $customField['type']
					 *
					 * Use the parent class method to display the fields
					*/
					$type = $customField['type'];

					switch ($type) {
						case 'text':

							static::text($customField);
							break;

						case 'textarea':

							static::textarea($customField);
							break;

						case 'checkbox':

							static::checkbox($customField);
							break;

						case 'checkboxes':

							static::checkboxes($customField);
							break;

						case 'radio':

							static::radio($customField);
							break;

						case 'select':

							static::select($customField);
							break;

                        case 'media':

                            static::media($customField);
                            break;

                        case 'infinite':

                            static::infinite($customField);
                            break;

                        case 'editor':

                            static::editor($customField);
                            break;

						default:
							static::text($customField);
							break;
					}

				}

			echo('</tbody>');

		echo('</table>');
	}
}
?>