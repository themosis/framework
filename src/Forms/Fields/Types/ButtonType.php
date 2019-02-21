<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\DataTransformerInterface;

class ButtonType extends BaseType implements DataTransformerInterface
{
    /**
     * ButtonType field view.
     *
     * @var string
     */
    protected $view = 'types.button';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'button';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.button';

    /**
     * Get default button options.
     *
     * @return array
     */
    public function getDefaultOptions(): array
    {
        $options = parent::getDefaultOptions();

        // Check the "type" attribute. If it is not set,
        // let's define it by default to "submit".
        if (! isset($options['attributes']['type'])) {
            $options['attributes']['type'] = 'submit';
        }

        // By default, disable button field type to be mapped
        // to a data object.
        $options['mapped'] = false;

        return $options;
    }

    /**
     * Parse field options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer($this);

        $options = parent::parseOptions($options);

        // Set some default CSS classes if chosen theme is "bootstrap".
        if (isset($options['theme']) && 'bootstrap' === $options['theme']) {
            $options['attributes']['class'] = isset($options['attributes']['class']) ?
                ' btn btn-primary' : 'btn btn-primary';
        }

        return $options;
    }

    /**
     * @inheritdoc
     *
     * @param mixed $data
     *
     * @return string
     */
    public function reverseTransform($data)
    {
        return $this->transform($data);
    }

    /**
     * @inheritdoc
     *
     * @param mixed $data
     *
     * @return string
     */
    public function transform($data)
    {
        return is_null($data) ? '' : (string) $data;
    }
}
