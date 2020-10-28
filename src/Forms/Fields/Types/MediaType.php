<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Core\Application;
use Themosis\Forms\Contracts\DataTransformerInterface;
use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Fields\Contracts\CanHandlePageSettings;
use Themosis\Forms\Fields\Contracts\CanHandleTerms;
use Themosis\Forms\Fields\Contracts\CanHandleUsers;
use Themosis\Forms\Fields\Exceptions\NotSupportedFieldException;
use Themosis\Forms\Resources\Transformers\MediaFieldTransformer;

class MediaType extends BaseType implements
    DataTransformerInterface,
    CanHandleMetabox,
    CanHandlePageSettings,
    CanHandleTerms,
    CanHandleUsers
{
    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'media';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.media';

    /**
     * The resource transformer class.
     *
     * @var string
     */
    protected $resourceTransformer = MediaFieldTransformer::class;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->allowedOptions = $this->setAllowedOptions();
        $this->defaultOptions = $this->setDefaultOptions();
    }

    /**
     * Define the field allowed options.
     *
     * @return array
     */
    protected function setAllowedOptions(): array
    {
        return array_merge($this->allowedOptions, [
            'type'
        ]);
    }

    /**
     * Define the field default options values.
     *
     * @return array
     */
    protected function setDefaultOptions(): array
    {
        $default = [
            'type' => ['image', 'application']
        ];

        if (function_exists('_x')) {
            $default['l10n'] = [
                'add' => _x('Add Media', 'field', Application::TEXTDOMAIN),
                'button' => _x('Insert', 'field', Application::TEXTDOMAIN),
                'id' => _x('ID:', 'field', Application::TEXTDOMAIN),
                'name' => _x('File Name:', 'field', Application::TEXTDOMAIN),
                'remove' => _x('Remove', 'field', Application::TEXTDOMAIN),
                'title' => _x('Insert Media', 'field', Application::TEXTDOMAIN)
            ];
        }

        return array_merge($this->defaultOptions, $default);
    }

    /**
     * Parse and setup default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer($this);

        return parent::parseOptions($options);
    }

    /**
     * @inheritdoc
     *
     * @param string|null $data
     *
     * @return int|string
     */
    public function transform($data)
    {
        return is_null($data) ? '' : (int) $data;
    }

    /**
     * @inheritdoc
     *
     * @param string $data
     *
     * @return int|string
     */
    public function reverseTransform($data)
    {
        return $this->transform($data);
    }

    /**
     * Handle media field post meta registration.
     *
     * @param string|int $value
     * @param int        $post_id
     */
    public function metaboxSave($value, int $post_id)
    {
        $this->setValue($value);

        $previous = get_post_meta($post_id, $this->getName(), true);

        if (is_null($this->getValue()) || empty($this->getValue())) {
            delete_post_meta($post_id, $this->getName());
        } elseif (empty($previous)) {
            add_post_meta($post_id, $this->getName(), $this->getRawValue(), true);
        } else {
            update_post_meta($post_id, $this->getName(), $this->getRawValue(), $previous);
        }
    }

    /**
     * Initialize media field value on a metabox context.
     *
     * @param int $post_id
     */
    public function metaboxGet(int $post_id)
    {
        $value = get_post_meta($post_id, $this->getName(), true);

        if (! empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Handle field term meta registration.
     *
     * @param string $value
     * @param int    $term_id
     *
     * @throws NotSupportedFieldException
     */
    public function termSave($value, int $term_id)
    {
        throw new NotSupportedFieldException('Field '.get_class($this).' is not supported on term meta.');
    }

    /**
     * Handle field term meta initial value.
     *
     * @param int $term_id
     *
     * @throws NotSupportedFieldException
     */
    public function termGet(int $term_id)
    {
        throw new NotSupportedFieldException('Field '.get_class($this).' is not supported on term meta.');
    }

    /**
     * Handle field user meta initial value.
     *
     * @param int $user_id
     *
     * @throws NotSupportedFieldException
     */
    public function userGet(int $user_id)
    {
        throw new NotSupportedFieldException('Field '.get_class($this).' is not supported on user meta.');
    }

    /**
     * Handle field user meta registration.
     *
     * @param array|string $value
     * @param int          $user_id
     *
     * @throws NotSupportedFieldException
     */
    public function userSave($value, int $user_id)
    {
        throw new NotSupportedFieldException('Field '.get_class($this).' is not supported on user meta.');
    }

    /**
     * Return the field setting value.
     *
     * @throws NotSupportedFieldException
     *
     * @return mixed
     */
    public function settingGet()
    {
        throw new NotSupportedFieldException('Field '.get_class($this).' is not supported on page settings.');
    }

    /**
     * Save the field setting value.
     *
     * @param mixed  $value
     * @param string $name
     */
    public function settingSave($value, string $name)
    {
        //
    }
}
