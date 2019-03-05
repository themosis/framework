<?php

namespace Themosis\Core\Auth;

use App\Forms\Auth\Passwords\EmailResetForm;
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
            return back()->withErrors($form->errors()->all());
        }

        $response = $this->broker()->sendResetLink([
            'email' => $form->repository()->getFieldByName('email')->getValue()
        ]);

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param Request $request
     * @param string  $response
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param Request $request
     * @param string  $response
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return back()->withErrors([
                'email' => trans($response)
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
