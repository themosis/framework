<?php
namespace Themosis\User;

use Themosis\Facades\Action;
use Themosis\View\IRenderable;

class UserFactory implements IUser
{
    /**
     * The user custom fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The user core/container view.
     *
     * @var IRenderable
     */
    protected $view;

    /**
     * Build a UserFactory instance.
     *
     * @param IRenderable $view The user core view.
     */
    public function __construct(IRenderable $view)
    {
        $this->view = $view;
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

        // WP_Error.
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
        return new User((int)$id);
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
     * Return a User instance using its ID.
     *
     * @param int $id
     * @return \Themosis\User\User
     */
    public function get($id)
    {
        return $this->createUser($id);
    }

    /**
     * Register sections for user custom fields.
     *
     * @param array $sections A list of sections to register.
     * @return \Themosis\User\IUser
     */
    public function addSections(array $sections)
    {
        // TODO: Implement addSections() method.
    }


    /**
     * Register custom fields for users.
     *
     * @param array $fields The user custom fields. By sections or not.
     * @return \Themosis\User\IUser
     */
    public function addFields(array $fields)
    {
        $this->fields = $fields;

        // User "display" events.
        // When adding/creating a new user.
        Action::add('user_new_form', [$this, 'displayFields']);
        // When editing another user profile.
        Action::add('edit_user_profile', [$this, 'displayFields']);
        // When editing its own profile.
        Action::add('show_user_profile', [$this, 'displayFields']);

        // User "save" events.
        Action::add('user_register', [$this, 'saveFields']);
        Action::add('profile_update', [$this, 'saveFields']);

        return $this;
    }

    /**
     * Render the user fields.
     *
     * @param \WP_User|string If adding a user, $user is the context (string): 'add-existing-user' for multisite, 'add.new-user' for single. Else is a \WP_User instance.
     * @return void
     */
    public function displayFields($user)
    {
        $this->view->with([
            '__fields' => $this->fields
        ]);

        echo($this->view->render());
    }

    /**
     * Triggered by the 'user_register' && 'profile_update' hooks.
     * Used in order to save custom fields for the users.
     *
     * @param int $id The user ID.
     * @param null|array $oldData Null by default. If user update, contains an array of previous user data.
     * @return void
     */
    public function saveFields($id, $oldData)
    {

    }


} 