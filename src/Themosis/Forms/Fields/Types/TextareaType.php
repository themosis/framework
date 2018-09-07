<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\DataTransformerInterface;

class TextareaType extends BaseType implements DataTransformerInterface
{
    /**
     * TextareaType field view.
     *
     * @var string
     */
    protected $view = 'types.textarea';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'textarea';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.textarea';

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
     * @param string $data
     *
     * @return string
     */
    public function transform($data)
    {
        return is_null($data) ? '' : (string) $data;
    }

    /**
     * @inheritdoc
     *
     * @param string $data
     *
     * @return string
     */
    public function reverseTransform($data)
    {
        return $this->transform($data);
    }
}
