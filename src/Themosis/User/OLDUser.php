<?php
namespace Themosis\User;

use WP_User;

class OLDUser
{
	/**
	 * User ID
	*/
	protected $id;

	/**
	 * WP_User object
	*/
	private $datas;

    /**
     * The User constructor.
     *
     * @param int $id The user ID.
     */
	private function __construct($id)
	{
		$this->id = $id;
		$this->datas = new WP_User($id);
	}

    /**
     * Create a new user and insert it in the DB. If user exists, returns
     * an instance of the user.
     *
     * @param string $username The user username.
     * @param string $password The user password.
     * @param string $email THe user email.
     * @throws UserException
     * @return \Themosis\User\User|\WP_Error A User instance or WP_Error in case of errors.
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
			throw new UserException("Invalid email address.");
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
     * @link http://codex.wordpress.org/Function_Reference/wp_update_user
	 * 
	 * @param array|object $userdata The user datas.
	 * @return \Themosis\User\User | bool
	 */
	public function update($userdata)
	{
		// Check if there is an ID in $userdata
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
			return $this;
		}

		return false;

	}

	/**
	 * Set the actual user role.
	 * 
	 * @param string $role The user role slug.
	 * @return \Themosis\User\User|bool A User instance or false if unable to set the user role.
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
	 * Return the user ID.
	 * 
	 * @return int The user ID.
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Return a user based on its ID.
	 * 
	 * @param int $id The user ID.
     * @return \WP_User
	 */
	public static function get($id = null)
	{
		if (is_numeric($id)) {
			return get_userdata($id);
		}

		return wp_get_current_user();
	}

	/**
	 * Checks if a defined user has a certain role.
	 *
	 * @param string $role The user role slug.
	 * @param int $userId The user ID.
	 * @return bool True. False if no role.
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
     * @link https://codex.wordpress.org/Function_Reference/current_user_can
	 * 
	 * @param string $cap The capability slug.
	 * @param int $id The user ID. By default, use the current user.
	 * @param mixed $args Check the current_user_can function arguments.
	 * @return bool True. False if user has no capability or role.
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