<?php

namespace Themosis\Core\Auth;

trait SendPasswordResetEmails
{
    /**
     * Show the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }
}
