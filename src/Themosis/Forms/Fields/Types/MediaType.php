<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Core\Application;
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
    protected function setAllowedOptions()
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
    protected function setDefaultOptions()
    {
        $default = [
            'type' => ['image', 'application']
        ];

        if (function_exists('_x')) {
            $default['l10n'] = [
                'add' => _x('Add Media', 'field', Application::TEXTDOMAIN),
                'button' => _x('Insert', 'field', Application::TEXTDOMAIN),
                'id' => _x('ID:', 'field', Application::TEXTDOMAIN),
                'name' => _x('Name:', 'field', Application::TEXTDOMAIN),
                'remove' => _x('Remove', 'field', Application::TEXTDOMAIN),
                'title' => _x('Insert Media', 'field', Application::TEXTDOMAIN)
            ];
        }

        return array_merge($this->defaultOptions, $default);
    }
}
