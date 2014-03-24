<?php
namespace Themosis\User;

use WP_User;

defined('DS') or die('No direct script access.');

class User
{
	/**
	 * User's id
	*/
	protected $id;

	/**
	 * WP_User object
	*/
	private $datas;

	private function __construct($id)
	{
		$this->id = $id;
		$this->datas = new WP_User($id);
	}

	/**
	 * Create a NEW user and insert it in the DB
	 * 
	 * @param string
	 * @param string
	 * @param string
	 * @return object (In case of errors, return an Error object)
	*/
	public static function make($username, $password, $email)
	{
		if (is_string($username) && !empty($username)) {
			$username = sanitize_user($username, true);
		} else {
			throw new UserException("Invalid username.");
		}

		if (is_string($password) && !empty($password)) {
			$password = sanitize_user($password);
		}

		if (is_email($email)) {
			$email = sanitize_email($email);
		} else {
			throw new UserException("Invalid email adress.");
		}

		// Create the user
		$user = wp_create_user($username, $password, $email);

		// If succeeds, is an INT
		if (is_int($user)) {

			return new static($user);

		// Check the errors property. If existing username, return the instance.
		} elseif (is_array($user->errors) == 1 && array_key_exists('existing_user_login', $user->errors)) {

			// Retrieve the user with that username
			$checkedUser = get_user_by('login', $username);

			// Check also the email
			$registeredEmail = $checkedUser->data->user_email;
			if ($registeredEmail === $email) {
				return new static($checkedUser->ID);	
			}
		}

		// Return the ERROR object
		return $user;
	}

	/**
	 * Update the user credentials.
	 * Use the params defined in the codex at:
	 * http://codex.wordpress.org/Function_Reference/wp_update_user
	 * 
	 * @param mixed array|object
	 * @return boolean
	*/
	public function update($userdata)
	{
		// Check if there is an ID in $userdatas
		// As stdClass
		if (is_a($userdata, 'stdClass')) {

			$userdata = get_object_vars($userdata);

		// As WP_User
		} elseif (is_a($userdata, 'WP_User')) {

			$userdata = $userdata->to_array();

		}
		
		if (is_array($userdata)) {
			if (!array_key_exists('ID', $userdata)) {

				$userdata['ID'] = $this->id;

			}
		}

		// Update the user credentials
		$update = wp_update_user($userdata);

		if (is_int($update)) {
			// Update object user data
			$this->datas = new WP_User($update);
			return true;
		}

		return false;

	}

	/**
	 * Set the actual user role.
	 * 
	 * @param string role slug
	 * @return mixed object|boolean
	*/
	public function setRole($role)
	{
		if (is_string($role) && !empty($role)) {
			$this->datas->set_role($role);
			return $this;
		}

		return false;
	}

	/**
	 * Return the USER id.
	 * 
	 * @return int
	*/
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Return a particular user based on its
	 * ID.
	 * 
	 * @param int (optional)
	*/
	public static function get($id = null)
	{
		if (is_numeric($id)) {
			return get_userdata($id);
		}

		return wp_get_current_user();
	}

	/**
	 * Checks if a defined user has a certain
	 * role.
	 *
	 * @param string role slug
	 * @param int (optional) user ID
	 * @return boolean
	 */
	public static function hasRole($role, $userId = null) {
	 
	    if (is_numeric($userId)) {
	    	$user = get_userdata($userId);

	    } else {
	    	$user = wp_get_current_user();

	    }
	 
	    if (!isset($user) || empty($user)) {
	    	return false;
	    }
	 
	    return in_array($role, (array) $user->roles );
	}

	/**
	 * Check if a user can do a defined
	 * capability or defined role.
	 * 
	 * @param string
	 * @param int (optional)
	 * @param mixed (current_user_can params)
	 * @return boolean
	*/
	public static function can($cap, $id = null, $args = null)
	{
		if (is_string($cap) && !empty($cap)) {
			
			if (is_numeric($id)) {

				return user_can((int) $id, $cap);

			}

			return current_user_can($cap, $args);

		}

		return false;
	}
}