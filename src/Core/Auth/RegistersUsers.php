<?php

namespace Themosis\Core\Auth;

trait RegistersUsers
{
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }
}
