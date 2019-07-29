<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Themosis\Forms\Contracts\FieldsRepositoryInterface;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Support\Contracts\SectionInterface;
use Themosis\User\Contracts\UserFieldContract;

/**
 * @method static UserFieldContract make(array $options = [])
 * @method static UserFieldContract add(FieldTypeInterface|SectionInterface $field, SectionInterface $section = null)
 * @method static UserFieldContract set()
 * @method static FieldsRepositoryInterface repository()
 * @method static mixed display(string|\WP_User $user)
 * @method static void save(int $user_id)
 *
 * @see \Themosis\User\UserField
 */
class UserField extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'themosis.user.field';
    }
}
