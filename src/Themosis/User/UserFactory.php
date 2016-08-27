<?php

namespace Themosis\User;

use Illuminate\View\View;
use Themosis\Field\Wrapper;
use Themosis\Field\FieldException;
use Themosis\Hook\IHook;
use Themosis\Validation\IValidate;

class UserFactory extends Wrapper implements IUser
{
    /**
     * The user sections.
     *
     * @var array
     */
    protected $sections = [];

    /**
     * The user custom fields.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Validator instance.
     *
     * @var IValidate
     */
    protected $validator;

    /**
     * Validation rules for custom fields.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * The user core/container view.
     *
     * @var \Illuminate\View\View
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
     * @var IHook
     */
    protected $action;

    /**
     * Build a UserFactory instance.
     *
     * @param \Illuminate\View\View $view      The user core view.
     * @param IValidate             $validator Validator instance.
     * @param IHook                 $action
     */
    public function __construct(View $view, IValidate $validator, IHook $action)
    {
        $this->view = $view;
        $this->validator = $validator;
        $this->action = $action;
    }

    /**
     * Create a new WordPress user.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     *
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
        if (is_int($user_id)) {
            return $this->createUser($user_id);
        } elseif (is_array($user_id->errors) && array_key_exists('existing_user_login', $user_id->errors)) {
            $user = get_user_by('login', $username);
            $registeredEmail = $user->data->user_email;

            // Compare the given email address before returning a user instance.
            if ($email === $registeredEmail) {
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
     *
     * @return \Themosis\User\User
     */
    protected function createUser($id)
    {
        return new User((int) $id);
    }

    /**
     * Check if given credentials to create a new WordPress user are valid.
     *
     * @param array $credentials
     *
     * @throws UserException
     */
    protected function parseCredentials(array $credentials)
    {
        foreach ($credentials as $name => $cred) {
            if ('email' === $name && !is_email($cred)) {
                throw new UserException("Invalid user property '{$name}'.");
            }

            if (!is_string($cred) || empty($cred)) {
                throw new UserException("Invalid user property '{$name}'.");
            }
        }
    }

    /**
     * Return a User instance using its ID.
     *
     * @param int $id
     *
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
     *
     * @return \Themosis\User\IUser
     */
    public function addSections(array $sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * Check if there are any sections defined.
     *
     * @return bool
     */
    public function hasSections()
    {
        return count($this->sections) ? true : false;
    }

    /**
     * Register custom fields for users.
     *
     * @param array  $fields     The user custom fields. By sections or not.
     * @param string $capability The minimum capability required to save user custom fields data.
     *
     * @return \Themosis\User\IUser
     */
    public function addFields(array $fields, $capability = 'edit_users')
    {
        $this->fields = $fields;
        $this->capability = $capability;

        // Check if there are sections before going further.
        $this->isUsingSections($fields);

        // User "display" events.
        // When adding/creating a new user.
        $this->action->add('user_new_form', [$this, 'displayFields']);
        // When editing another user profile.
        $this->action->add('edit_user_profile', [$this, 'displayFields']);
        // When editing its own profile.
        $this->action->add('show_user_profile', [$this, 'displayFields']);

        // User "save" events.
        $this->action->add('user_register', [$this, 'saveFields']);
        $this->action->add('profile_update', [$this, 'saveFields']);

        return $this;
    }

    /**
     * Check if the defined fields are using the sections defined or not.
     * If there are sections and the fields are not set to use a section,
     * trigger an error.
     *
     * @param array $fields
     *
     * @throws \Themosis\User\UserException
     */
    protected function isUsingSections(array $fields)
    {
        if ($this->hasSections()) {
            foreach ($this->sections as $section) {
                $section = $section->getData();

                // Check if fields are defined per section.
                if (!isset($fields[$section['slug']])) {
                    throw new UserException("There are no user custom fields defined for the section: {$section['slug']}.");
                }
            }
        }
    }

    /**
     * Register validation rules for user custom fields.
     *
     * @param array $rules
     *
     * @return \Themosis\User\IUser
     */
    public function validate(array $rules = [])
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Render the user fields.
     *
     * @param \WP_User|string If adding a user, $user is the context (string): 'add-existing-user' for multisite, 'add.new-user' for single. Else is a \WP_User instance.
     */
    public function displayFields($user)
    {
        // Add nonce fields for safety.
        if (!static::$hasNonce) {
            wp_nonce_field('user', '_themosisnonce');
            static::$hasNonce = true;
        }

        // Set the value attribute for each field.
        $fields = $this->setDefaultValue($user, $this->fields);

        // User view data
        $params = [
            '__factory' => $this,
            '__fields' => $fields,
            '__sections' => $this->sections,
            '__user' => $user,
            '__userContext' => null,
        ];

        // Check if $user is a string context
        if (is_string($user)) {
            // Set to null __user
            $params['__user'] = null;

            // Set the context
            $params['__userContext'] = $user;
        }

        // Pass data to user view.
        $this->view->with($params);

        // Render the fields.
        echo $this->view->render();
    }

    /**
     * Set the default 'value' property for all fields.
     *
     * @param \WP_User|string $user
     * @param array           $fields
     *
     * @return array
     */
    protected function setDefaultValue($user, $fields)
    {
        $theFields = [];

        foreach ($fields as $section => $fs) {
            // If Add User screen, set the ID to 0, so the value is empty.
            $id = (is_a($user, 'WP_User')) ? $user->ID : 0;

            // It's a section...
            if (!is_numeric($section)) {
                foreach ($fs as $f) {
                    $value = get_user_meta($id, $f['name'], true);
                    $f['value'] = $this->parseValue($f, $value);
                    $theFields[$section][] = $f;
                }
            } else {
                // Simple fields
                $value = get_user_meta($id, $fs['name'], true);
                $fs['value'] = $this->parseValue($fs, $value);
                $theFields[] = $fs;
            }
        }

        return $theFields;
    }

    /**
     * Triggered by the 'user_register' && 'profile_update' hooks.
     * Used in order to save custom fields for the users.
     *
     * @param int   $id      The user ID.
     * @param array $oldData Null by default. If user update, contains an array of previous user data.
     *
     * @throws FieldException
     */
    public function saveFields($id, $oldData = [])
    {
        // Check capability
        $user = new \WP_User($id);
        if (!in_array($this->capability, $user->allcaps)) {
            return;
        }

        // Check nonces
        $nonceName = (isset($_POST['_themosisnonce'])) ? $_POST['_themosisnonce'] : '_themosisnonce';
        if (!wp_verify_nonce($nonceName, 'user')) {
            return;
        }

        // Loop through the fields...
        $fields = $this->parseTheFields($this->fields);

        // Register user meta.
        $this->register($id, $fields);
    }

    /**
     * Register custom user meta.
     *
     * @param int   $id     The user_ID
     * @param array $fields The custom fields to register.
     */
    protected function register($id, $fields)
    {
        foreach ($fields as $field) {
            $value = isset($_POST[$field['name']]) ? $_POST[$field['name']] : $this->parseValue($field);

            // Validation code...
            if (isset($this->rules[$field['name']])) {
                $rules = $this->rules[$field['name']];

                // Check for infinite field (if $rules is an associative array).
                if ($this->validator->isAssociative($rules) && 'infinite' == $field->getFieldType()) {
                    // Check infinite fields validation.
                    foreach ($value as $row => $rowValues) {
                        foreach ($rowValues as $name => $val) {
                            if (isset($rules[$name])) {
                                $value[$row][$name] = $this->validator->single($val, $rules[$name]);
                            }
                        }
                    }
                } else {
                    $value = $this->validator->single($value, $this->rules[$field['name']]);
                }
            }

            // Register the user meta data.
            update_user_meta($id, $field['name'], $value);
        }
    }

    /**
     * Parse the list of registered fields. Remove the sections if defined.
     *
     * @param array $fields The fields.
     *
     * @return array A clean list of fields.
     *
     * @throws FieldException
     */
    protected function parseTheFields(array $fields)
    {
        $theFields = [];

        foreach ($fields as $section => $fs) {
            // Sections defined.
            if (!is_numeric($section)) {
                foreach ($fs as $f) {
                    if (!is_a($f, '\Themosis\Field\Fields\IField')) {
                        throw new FieldException('An IField instance is necessary in order to save custom user data.');
                    }
                    $theFields[] = $f;
                }
            } else {
                if (!is_a($fs, '\Themosis\Field\Fields\IField')) {
                    throw new FieldException('An IField instance is necessary in order to save custom user data.');
                }
                $theFields[] = $fs;
            }
        }

        return $theFields;
    }
}
