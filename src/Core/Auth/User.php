<?php

namespace Themosis\Core\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableInterface;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordInterface;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Themosis\Core\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableInterface,
    AuthorizableInterface,
    CanResetPasswordInterface,
    MustVerifyEmailInterface
{
    use Authenticatable, Authorizable, CanResetPassword, MustVerifyEmail, Notifiable;
}
