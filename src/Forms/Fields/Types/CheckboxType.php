<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Fields\Contracts\CanHandlePageSettings;
use Themosis\Forms\Fields\Contracts\CanHandleTerms;
use Themosis\Forms\Fields\Contracts\CanHandleUsers;
use Themosis\Forms\Transformers\StringToBooleanTransformer;

class CheckboxType extends BaseType implements
    CanHandleMetabox,
    CanHandlePageSettings,
    CanHandleTerms,
    CanHandleUsers
{
    /**
     * CheckboxType field view.
     *
     * @var string
     */
    protected $view = 'types.checkbox';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'checkbox';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.checkbox';

    /**
     * Parse field options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new StringToBooleanTransformer());

        $options = parent::parseOptions($options);

        // Set some default CSS classes if chosen theme is "bootstrap".
        if (isset($options['theme']) && 'bootstrap' === $options['theme']) {
            $options['attributes']['class'] = isset($options['attributes']['class']) ?
                'form-check-input '.$options['attributes']['class'] : 'form-check-input';
            $options['label_attr']['class'] = isset($options['label_attr']['class']) ?
                'form-check-label '.$options['label_attr']['class'] : 'form-check-label';
        }

        return $options;
    }

    /**
     * @inheritdoc
     *
     * @param string $value
     *
     * @return FieldTypeInterface
     */
    public function setValue($value): FieldTypeInterface
    {
        parent::setValue($value);

        if ($this->getValue()) {
            // The value is only set on the field when it fails
            // or when the option "flush" is set to "false".
            // If true, let's automatically add the "checked" attribute.
            $this->options['attributes']['checked'] = 'checked';
        } elseif (false === $this->getValue() && isset($this->options['attributes']['checked'])) {
            unset($this->options['attributes']['checked']);
        }

        return $this;
    }

    /**
     * Handle checkbox field post meta registration.
     *
     * @param mixed $value
     * @param int   $post_id
     */
    public function metaboxSave($value, int $post_id)
    {
        $this->setValue($value);

        $previous = get_post_meta($post_id, $this->getName(), true);

        if (is_null($this->getValue())) {
            delete_post_meta($post_id, $this->getName());
        } elseif (empty($previous)) {
            add_post_meta($post_id, $this->getName(), $this->getRawValue(), true);
        } else {
            update_post_meta($post_id, $this->getName(), $this->getRawValue(), $previous);
        }
    }

    /**
     * Initialize the checkbox field post meta value.
     *
     * @param int $post_id
     */
    public function metaboxGet(int $post_id)
    {
        $value = get_post_meta($post_id, $this->getName(), true);

        if (! empty($value) && is_string($value)) {
            $this->setValue($value);
        } elseif (empty($value) && ! empty($this->getOption('data'))) {
            $this->setValue($this->getOption('data'));
        }
    }

    /**
     * Handle field term meta registration.
     *
     * @param string $value
     * @param int    $term_id
     */
    public function termSave($value, int $term_id)
    {
        $this->setValue($value);

        $previous = get_term_meta($term_id, $this->getName(), true);

        if (is_null($this->getValue()) || empty($this->getValue())) {
            delete_term_meta($term_id, $this->getName());
        } elseif (empty($previous)) {
            add_term_meta($term_id, $this->getName(), $this->getRawValue(), true);
        } else {
            update_term_meta($term_id, $this->getName(), $this->getRawValue(), $previous);
        }
    }

    /**
     * Handle field term meta initial value.
     *
     * @param int $term_id
     */
    public function termGet(int $term_id)
    {
        $value = get_term_meta($term_id, $this->getName(), true);

        if (! empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Handle field user meta initial value.
     *
     * @param int $user_id
     */
    public function userGet(int $user_id)
    {
        $value = get_user_meta($user_id, $this->getName(), true);

        if (! empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     * Handle field user meta registration.
     *
     * @param array|string $value
     * @param int          $user_id
     */
    public function userSave($value, int $user_id)
    {
        $this->setValue($value);

        $previous = get_user_meta($user_id, $this->getName(), true);

        if (is_null($this->getValue()) || empty($this->getValue())) {
            delete_user_meta($user_id, $this->getName());
        } elseif (empty($previous)) {
            add_user_meta($user_id, $this->getName(), $this->getRawValue(), true);
        } else {
            update_user_meta($user_id, $this->getName(), $this->getRawValue(), $previous);
        }
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

    /**
     * Return the field setting value.
     *
     * @return mixed|void
     */
    public function settingGet()
    {
        //
    }
}
