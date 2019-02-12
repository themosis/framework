<?php

namespace Themosis\Core\Auth;

use App\Forms\Auth\Password\EmailResetForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

trait SendPasswordResetEmails
{
    /**
     * Show the form to request a password reset link.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        $form = $this->form(new EmailResetForm());

        return view('auth.passwords.email', [
            'form' => $form
        ]);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $form = $this->form(new EmailResetForm());

        $form->handleRequest($request);

        if ($form->isNotValid()) {
            return redirect('password.request')->with([
                'form' => $form
            ]);
        }

        $response = $this->broker()->sendResetLink([
            'email' => $form->repository()->getFieldByName('email')->getValue()
        ]);
    }

    /**
     * Return the broker to use during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}
