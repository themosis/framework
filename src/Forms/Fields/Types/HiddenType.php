<?php

namespace Themosis\Forms\Fields\Types;

use Themosis\Forms\Fields\Exceptions\NotSupportedFieldException;

class HiddenType extends TextType
{
    /**
     * HiddenType field view.
     *
     * @var string
     */
    protected $view = 'types.hidden';

    /**
     * Field type.
     *
     * @var string
     */
    protected $type = 'hidden';

    /**
     * The component name.
     *
     * @var string
     */
    protected $component = 'themosis.fields.hidden';

    /**
     * Handle hidden field post meta registration.
     * We do not allow registration as this is a read-only field.
     *
     * @param string $value
     * @param int    $post_id
     *
     * @return null|void
     */
    public function metaboxSave($value, int $post_id)
    {
        return null;
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
