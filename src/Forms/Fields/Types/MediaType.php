<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Core\Application;
use Themosis\Forms\Fields\Exceptions\NotSupportedFieldException;
use Themosis\Forms\Resources\Transformers\MediaFieldTransformer;

class MediaType extends IntegerType
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
