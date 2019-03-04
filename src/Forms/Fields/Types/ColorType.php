<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Core\Application;
use Themosis\Forms\Fields\Exceptions\NotSupportedFieldException;

class ColorType extends TextType
{
    /**
     * Color field view.
     *
     * @var string
     */
    protected $view = 'types.text';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'color';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.color';

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
            'colors',
            'disableCustomColors'
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
            'colors' => $this->getDefaultColors(),
            'disableCustomColors' => false
        ];

        if (function_exists('_x')) {
            $default['l10n'] = [
                'clear' => _x('Clear', 'field', Application::TEXTDOMAIN)
            ];
        }

        return array_merge($this->defaultOptions, $default);
    }

    /**
     * Return a list of default colors for the field.
     *
     * @return array
     */
    protected function getDefaultColors()
    {
        $colors = [
            [
                'name' => 'Pale pink',
                'color' => '#f78da7'
            ],
            [
                'name' => 'Vivid red',
                'color' => '#cf2e2e'
            ],
            [
                'name' => 'Luminous vivid orange',
                'color' => '#ff6900'
            ],
            [
                'name' => 'Luminous vivid amber',
                'color' => '#fcb900'
            ],
            [
                'name' => 'Light green cyan',
                'color' => '#7bdcb5'
            ],
            [
                'name' => 'Vivid green cyan',
                'color' => '#00d084'
            ],
            [
                'name' => 'Pale cyan blue',
                'color' => '#8ed1fc'
            ],
            [
                'name' => 'Vivid cyan blue',
                'color' => '#0693e3'
            ],
            [
                'name' => 'Very light gray',
                'color' => '#eeeeee'
            ],
            [
                'name' => 'Cyan bluish gray',
                'color' => '#abb8c3'
            ],
            [
                'name' => 'Very dark gray',
                'color' => '#313131'
            ]
        ];

        return array_map(function (array $color) {
            return [
                'name' => function_exists('__') ? __($color['name'], Application::TEXTDOMAIN) : $color['name'],
                'color' => $color['color']
            ];
        }, $colors);
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
     * @param string $value
     * @param int    $user_id
     *
     * @throws NotSupportedFieldException
     */
    public function userSave($value, int $user_id)
    {
        throw new NotSupportedFieldException('Field '.get_class($this).' is not supported on user meta.');
    }
}
