<?php

namespace Themosis\User;

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;
use Illuminate\View\Factory as ViewFactory;
use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\Contracts\CanHandleUsers;
use Themosis\Forms\Fields\Types\BaseType;
use Themosis\Hook\IHook;
use Themosis\Support\Contracts\SectionInterface;
use Themosis\Support\Section;
use Themosis\User\Contracts\UserFieldContract;
use Themosis\User\Exceptions\UserException;

class UserField implements UserFieldContract
{
    /**
     * @var FieldsRepositoryInterface
     */
    protected $repository;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var ViewFactory
     */
    protected $factory;

    /**
     * @var ValidationFactory
     */
    protected $validator;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $allowedOptions = [
        'prefix'
    ];

    /**
     * @var array
     */
    protected $defaultOptions = [
        'prefix' => 'th_'
    ];

    public function __construct(
        FieldsRepositoryInterface $repository,
        IHook $action,
        ViewFactory $factory,
        ValidationFactory $validator
    ) {
        $this->repository = $repository;
        $this->action = $action;
        $this->factory = $factory;
        $this->validator = $validator;
    }

    /**
     * Build user fields.
     *
     * @param array $options
     *
     * @return UserFieldContract
     */
    public function make(array $options = []): UserFieldContract
    {
        $this->options = $this->parseOptions($options);

        return $this;
    }

    /**
     * Parse options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options)
    {
        $options = array_merge($this->defaultOptions, $options);

        foreach ($options as $option => $value) {
            if (! in_array($option, $this->allowedOptions, true)) {
                throw new \InvalidArgumentException("Option [$option] is not allowed.");
            }
        }

        return $options;
    }

    /**
     * Add a user field.
     *
     * @param \Themosis\Forms\Contracts\FieldTypeInterface|SectionInterface $field
     * @param SectionInterface|null                                         $section
     *
     * @return UserFieldContract
     */
    public function add($field, SectionInterface $section = null): UserFieldContract
    {
        if ($field instanceof SectionInterface) {
            $section = $field;

            if ($section->hasItems()) {
                foreach ($section->getItems() as $item) {
                    /** @var FieldTypeInterface $item */
                    $item->setOptions([
                        'group' => $section->getId()
                    ]);

                    $this->add($item, $section);
                }
            }

            return $this;
        }

        // Setup field options
        $field->setTheme('themosis.users');
        $field->setPrefix($this->options['prefix']);

        $this->repository->addField($field, $this->getSection($field, $section));

        return $this;
    }

    /**
     * Get section for given field.
     *
     * @param FieldTypeInterface $field
     * @param null               $section
     *
     * @return SectionInterface
     */
    protected function getSection(FieldTypeInterface $field, $section = null): SectionInterface
    {
        if (is_null($section)) {
            if ($this->repository->hasGroup($field->getOption('group'))) {
                $section = $this->repository->getGroup($field->getOption('group'));
            } else {
                $section = new Section($field->getOption('group'));
            }

            $section->addItem($field);
        }

        return $section;
    }

    /**
     * Set the user fields.
     *
     * @return UserFieldContract
     */
    public function set(): UserFieldContract
    {
        $this->action->add([
            'user_new_form',
            'show_user_profile',
            'edit_user_profile'
        ], [$this, 'display']);

        $this->action->add([
            'user_register',
            'profile_update'
        ], [$this, 'save']);

        return $this;
    }

    /**
     * Return the fields repository.
     *
     * @return FieldsRepositoryInterface
     */
    public function repository(): FieldsRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Display user meta fields.
     *
     * If adding a user, $user is a string and represent
     * a context: 'add-existing-user' (multisite),
     * 'add-new-user' (single). Else is a WP_User instance.
     *
     * @param mixed $user
     */
    public function display($user)
    {
        foreach ($this->repository->getGroups() as $group) {
            /** @var SectionInterface $group */
            $fields = $group->getItems();

            if (is_a($user, 'WP_User')) {
                // Initiate the value (edit screens only).
                array_walk($fields, function ($field) use ($user) {
                    /** @var CanHandleUsers $field */
                    $field->userGet($user->ID);
                });
            }

            echo $this->factory->make('themosis.users.main', [
                'section' => $group,
                'fields' => $fields
            ])->render();
        }
    }

    /**
     * Save user meta.
     *
     * By default, the callback always contains the user_id as
     * the first parameter. In the case of an update profile,
     * a second parameter is available containing an array
     * of previous user meta data.
     *
     * @param int $user_id
     *
     * @throws UserException
     */
    public function save($user_id)
    {
        $validator = $this->validator->make(
            $this->getUserData(app('request')),
            $this->getUserRules(),
            $this->getUserMessages(),
            $this->getUserPlaceholders()
        );

        $validation = $validator->valid();

        foreach ($this->repository->all() as $field) {
            /** @var FieldTypeInterface|BaseType|CanHandleUsers $field */
            $field->setErrorMessageBag($validator->errors());

            if (method_exists($field, 'userSave')) {
                $field->userSave($validation[$field->getName()] ?? null, $user_id);
            } else {
                throw new UserException('Unable to save ['.$field->getName().']. The [userSave] method is missing.');
            }
        }
    }

    /**
     * Return request user meta data.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getUserData(Request $request)
    {
        $data = [];

        foreach ($this->repository->all() as $field) {
            $data[$field->getName()] = $request->get($field->getName());
        }

        return $data;
    }

    /**
     * Return user validation rules.
     *
     * @return array
     */
    protected function getUserRules()
    {
        $rules = [];

        foreach ($this->repository->all() as $field) {
            $rules[$field->getName()] = $field->getOption('rules');
        }

        return $rules;
    }

    /**
     * Return user validation messages.
     *
     * @return array
     */
    protected function getUserMessages()
    {
        $messages = [];

        foreach ($this->repository->all() as $field) {
            foreach ($field->getOption('messages') as $rule => $message) {
                $messages[$field->getName().'.'.$rule] = $message;
            }
        }

        return $messages;
    }

    /**
     * Return user validation placeholders.
     *
     * @return array
     */
    protected function getUserPlaceholders()
    {
        $placeholders = [];

        foreach ($this->repository->all() as $field) {
            $placeholders[$field->getName()] = $field->getOption('placeholder');
        }

        return $placeholders;
    }
}
