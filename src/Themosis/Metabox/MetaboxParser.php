<?php
namespace Themosis\Metabox;

use Themosis\Session\Session;
use Themosis\User\User;

defined('DS') or die('No direct script access.');

class MetaboxParser
{
	/**
	 * Capability required
	*/
	private $cap;

	/**
	 * Cap args
	*/
	private $args = null;

	/**
	 * Check user capability
	*/
	private $userCan = false;

	/**
	 * Metabox postType
	*/
	private $postType;

	/**
	 * User ID. User that can register the datas.
	*/
	private $userId = null;

	/**
	 * Metabox datas
	*/
	private $datas;

	public function __construct(MetaboxData $datas)
	{
		$this->datas = $datas;
	}

	/**
	 * Set the page type where the metabox is saved.
	*/
	public function setType($postType)
	{
		$this->postType = $postType;
	}

	/**
	 * Set user capability check for the custom metabox
	 *
	 * @param string
	 * @param int
	 * @param mixed (optional)
	*/
	public function userCheck($cap, $userId = null, $args = null)
	{
		$this->cap = (is_string($cap)) ? $cap : null;
		$this->userId = (is_numeric($userId)) ? $userId : null;
		$this->args = $args;
		$this->userCan = true;
	}

	/**
	 * Handle the save methodology for each metabox.
	 * Receive the post it belongs to and the metabox datas.
	 *
	 * @param int
	*/
	public function save($inputs, $postId)
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		$nonceName = (isset($_POST[Session::nonceName])) ? $_POST[Session::nonceName] : Session::nonceName;
	    if (!wp_verify_nonce($nonceName, Session::nonceAction)) return;

	    // By default, check for these user permissions.
	    if (!$this->userCan) {

	    	if ('page' === $_POST['post_type']) {
				if (!User::can('edit_page', null, $postId))
				return;
			} else {
				if (!User::can('edit_post', null, $postId))
				return;
			}

		// Otherwise, check for the one that is set !
	    } else {
	    	if (!isset($this->args) || is_null($this->args)) {
	    		$this->args = $postId;
	    	}
	    	if (!User::can($this->cap, $this->userId, $this->args)) return;
	    }

		foreach ($inputs as $input) {

			$type = $input['type'];

			switch ($type) {

				case 'text':

					if (isset($_POST[$input['name']])) {

						$value = strip_tags(trim($_POST[$input['name']]));

						update_post_meta($postId, $input['name'], $value);

					}

					break;

				case 'textarea':

					if (isset($_POST[$input['name']])) {

						$value = esc_textarea($_POST[$input['name']]);

						update_post_meta($postId, $input['name'], $value);

					}

					break;

				case 'checkbox':

					if (isset($_POST[$input['name']])) {

						update_post_meta($postId, $input['name'], $_POST[$input['name']]);

					} else {

						$value = 'off';

						update_post_meta($postId, $input['name'], $value);

					}

					break;

				case 'checkboxes':

					if (isset($_POST[$input['name']])) {

						update_post_meta($postId, $input['name'], $_POST[$input['name']]);

					} else {

						$value = array();

						update_post_meta($postId, $input['name'], $value);

					}

					break;

				case 'radio':

					if (isset($_POST[$input['name']])) {

						update_post_meta($postId, $input['name'], $_POST[$input['name']]);

					} else {

						$value = array();

						update_post_meta($postId, $input['name'], $value);

					}

					break;

				case 'select':

					if (isset($_POST[$input['name']])) {

						$value = $_POST[$input['name']];

						// Could be a string or an array - Let's let the conditionnal statements for now...
						if (is_string($value)) {

							update_post_meta($postId, $input['name'], $value);

						} else if (is_array($value)) {

							update_post_meta($postId, $input['name'], $value);

						}

					} else {

						// If not set, probably it's missing an empty array
						$value = array();

						update_post_meta($postId, $input['name'], $value);

					}

					break;

				case 'media':

					if (isset($_POST[$input['name']])) {

						$value = strip_tags(trim($_POST[$input['name']]));

						update_post_meta($postId, $input['name'], $value);

					}

					break;

				case 'infinite':

					if (isset($_POST[$input['name']])) {

						$value = $_POST[$input['name']];

						update_post_meta($postId, $input['name'], $value);

					}

					break;

				case 'editor':

				    if (isset($_POST[$input['name']])) {

    				    $value = $_POST[$input['name']];

    				    update_post_meta($postId, $input['name'], $value);

				    }

				    break;

				default:

					if (isset($_POST[$input['name']])) {

						$value = strip_tags(trim($_POST[$input['name']]));

						update_post_meta($postId, $input['name'], $value);

					}

					break;
			} // END SWITCH

		} // END FOREACH

	} //END SAVE METHOD

}