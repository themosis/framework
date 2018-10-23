<?php

namespace Themosis\Forms\Fields\Types;

class EditorType extends TextareaType
{
    /**
     * Field view.
     *
     * @var string
     */
    protected $view = 'types.editor';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'editor';

    /**
     * Field component.
     *
     * @var string
     */
    protected $component = 'themosis.fields.editor';

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->allowedOptions = $this->setAllowedOptions();
        $this->defaultOptions = $this->setDefaultOptions();
    }

    /**
     * Return field allowed options.
     *
     * @return array
     */
    protected function setAllowedOptions(): array
    {
        return array_merge($this->allowedOptions, [
            'settings',
            'settings_js'
        ]);
    }

    /**
     * Return field default options values.
     *
     * @return array
     */
    protected function setDefaultOptions(): array
    {
        return array_merge($this->defaultOptions, [
            'settings' => [
                'textarea_rows' => 10
            ],
            'settings_js' => [
                'quicktags' => true,
                'tinymce' => [
                    'min_height' => 200
                ]
            ]
        ]);
    }
}
