<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Fields\Contracts\CanHandleMetabox;
use Themosis\Forms\Fields\Contracts\CanHandlePageSettings;
use Themosis\Forms\Fields\Contracts\CanHandleTerms;
use Themosis\Forms\Fields\Contracts\CanHandleUsers;
use Themosis\Forms\Transformers\IntegerToLocalizedStringTransformer;

class IntegerType extends BaseType implements
    CanHandleMetabox,
    CanHandlePageSettings,
    CanHandleTerms,
    CanHandleUsers
{
    /**
     * IntegerType field view.
     *
     * @var string
     */
    protected $view = 'types.number';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'integer';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.integer';

    /**
     * Parse and setup default options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function parseOptions(array $options): array
    {
        $this->setTransformer(new IntegerToLocalizedStringTransformer($this->getLocale(), $this));

        return parent::parseOptions($options);
    }

    /**
     * Handle integer field post meta registration.
     *
     * @param string $value
     * @param int    $post_id
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
     * Initialize the integer field value.
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
