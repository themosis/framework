<?php
namespace Themosis\User;

use Themosis\Core\Wrapper;
use Themosis\Facades\Action;
use Themosis\Field\FieldException;
use Themosis\Session\Session;
use Themosis\View\IRenderable;

class UserFactory extends Wrapper implements IUser
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
     * The capability in order to save custom data.
     *
     * @var string
     */
    protected $capability = 'edit_users';

    /**
     * Globally check if nonce inputs are inserted.
     *
     * @var bool
     */
    protected static $hasNonce = false;

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
     * @param string $capability The minimum capability required to save user custom fields data.
     * @return \Themosis\User\IUser
     */
    public function addFields(array $fields, $capability = 'edit_users')
    {
        $this->fields = $fields;
        $this->capability = $capability;

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
        // Add nonce fields for safety.
        if (!static::$hasNonce)
        {
            wp_nonce_field(Session::nonceAction, Session::nonceName);
            static::$hasNonce = true;
        }

        // Set the default value for all fields.
        // Get a proper list of fields (without their sections).
        $fields = $this->parseTheFields($this->fields);
        // Set the value attribute for each field.
        $fields = $this->setDefaultValue($user, $fields);

        // User view data
        $params = [
            '__fields'      => $fields,
            '__user'        => $user,
            '__userContext' => null
        ];

        // Check if $user is a string context
        if (is_string($user))
        {
            // Set to null __user
            $params['__user'] = null;

            // Set the context
            $params['__userContext'] = $user;
        }

        // Pass data to user view.
        $this->view->with($params);

        // Render the fields.
        echo($this->view->render());
    }

    /**
     * Set the default 'value' property for all fields.
     *
     * @param \WP_User|string $user
     * @param array $fields
     * @return array
     */
    protected function setDefaultValue($user, $fields)
    {
        $theFields = [];

        foreach ($fields as $field)
        {
            // Check if saved value.
            // If Add User screen, set the ID to 0, so the value is empty.
            $id = (is_a($user, 'WP_User')) ? $user->ID : 0;
            $value = get_user_meta($id, $field['name'], true);

            $field['value'] = $this->parseValue($field, $value);

            $theFields[] = $field;
        }

        return $theFields;
    }

    /**
     * Triggered by the 'user_register' && 'profile_update' hooks.
     * Used in order to save custom fields for the users.
     *
     * @param int $id The user ID.
     * @param null|array $oldData Null by default. If user update, contains an array of previous user data.
     * @throws FieldException
     * @return void
     */
    public function saveFields($id, $oldData)
    {
        // Check capability
        if (!current_user_can($this->capability)) return;

        // Check nonces
        $nonceName = (isset($_POST[Session::nonceName])) ? $_POST[Session::nonceName] : Session::nonceName;
        if (!wp_verify_nonce($nonceName, Session::nonceAction)) return;

        // Loop through the fields...
        $fields = $this->parseTheFields($this->fields);

        // Register user meta.
        $this->register($id, $fields);
    }

    /**
     * Register custom user meta.
     *
     * @param int $id The user_ID
     * @param array $fields The custom fields to register.
     * @return void
     */
    protected function register($id, $fields)
    {
        foreach ($fields as $field)
        {
            $value = isset($_POST[$field['name']]) ? $_POST[$field['name']] : $this->parseValue($field);

            // Validation code...

            // Register the user meta data.
            update_user_meta($id, $field['name'], $value);
        }
    }

    /**
     * Parse the list of registered fields. Remove the sections if defined.
     *
     * @param array $fields The fields.
     * @return array A clean list of fields.
     * @throws FieldException
     */
    protected function parseTheFields(array $fields)
    {
        $theFields = [];

        foreach ($fields as $section => $fs)
        {
            // Sections defined.
            if (!is_numeric($section))
            {
                foreach ($fs as $f)
                {
                    if (!is_a($f, '\Themosis\Field\Fields\IField')) throw new FieldException('An IField instance is necessary in order to save custom user data.');
                    $theFields[] = $f;
                }
            }
            else
            {
                $theFields[] = $fs;
            }
        }

        return $theFields;
    }

} 