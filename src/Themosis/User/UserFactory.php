<?php
namespace Themosis\User;

use Themosis\Facades\Action;
use Themosis\Action\Action as OldAction;

class UserFactory
{
    /**
     * A list of user instances.
     *
     * @var array
     */
    protected static $instances = [];

    /**
     * Build a UserFactory instance.
     */
    public function __construct()
    {
        // User "display" events.
        // When adding/creating a new user.
        Action::add('user_new_form', [$this, 'display']);
        // When editing another user profile.
        Action::add('edit_user_profile', [$this, 'display']);
        // When editing its own profile.
        Action::add('show_user_profile', [$this, 'display']);

        // User "save" events.
        Action::add('user_register', [$this, 'userRegister']);
        Action::add('profile_update', [$this, 'userRegister']);
    }

    /**
     * Create a new WordPress user.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @return \Themosis\User\User | \WP_Error
     */
    public function make($username, $password, $email)
    {
        $this->parseCredentials(compact('username', 'password', 'email'));

        // Clean credentials.
        $username = sanitize_user($username, true);
        $password = sanitize_user($password);
        $email = sanitize_email($email);

        // Create a WordPress in the database.
        $user_id = wp_create_user($username, $password, $email);

        // If user created.
        if (is_int($user_id))
        {
            return $this->createUser($user_id);
        }
        elseif (is_array($user_id->errors) && array_key_exists('existing_user_login', $user_id->errors))
        {
            $user = get_user_by('login', $username);
            $registeredEmail = $user->data->user_email;

            // Compare the given email address before returning a user instance.
            if ($email === $registeredEmail)
            {
                return $this->createUser($user->ID);
            }
        }

        // Error.
        return $user_id;
    }

    /**
     * Look at the current user and return an instance.
     *
     * @return \Themosis\User\User
     */
    public function current()
    {
        $user = wp_get_current_user();

        return $this->createUser($user->ID);
    }

    /**
     * Create a new User instance.
     *
     * @param int $id
     * @return \Themosis\User\User
     */
    protected function createUser($id)
    {
        if (isset(static::$instances[$id])) return static::$instances[$id];

        return static::$instances[$id] = new User((int)$id);
    }

    /**
     * Check if given credentials to create a new WordPress user are valid.
     *
     * @param array $credentials
     * @throws UserException
     * @return void
     */
    protected function parseCredentials(array $credentials)
    {
        foreach ($credentials as $name => $cred)
        {
            if ('email' === $name && !is_email($cred))
            {
                throw new UserException("Invalid user property '{$name}'.");
            }

            if (!is_string($cred) || empty($cred))
            {
                throw new UserException("Invalid user property '{$name}'.");
            }
        }
    }

    /**
     * Return a User instance from the registered list using its ID.
     *
     * @param int $id
     * @return \Themosis\User\User
     */
    public function get($id)
    {
        return $this->add($id);
    }

    /**
     * Add a registered user to the UserFactory list.
     *
     * @param null $id
     * @return \Themosis\User\User|bool
     */
    public function add($id)
    {
        $user = get_userdata((int)$id);

        if (false !== $user)
        {
            return $this->createUser($user->ID);
        }

        return $user;
    }

    /**
     * Triggered by the 'user_register' hook.
     * Add a new registered user to the UserFactory list at runtime.
     *
     * @param int $id
     * @param
     * @return void
     */
    public function userRegister($id)
    {
        // Keep the list of users up-to-date when new users are inserted from the wp-admin.
        $this->createUser($id);
    }

    public function display()
    {
        ?>
        <h3>Extra profile information</h3>
        <?php
    }

} 