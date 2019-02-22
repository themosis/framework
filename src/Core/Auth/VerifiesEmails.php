<?php

namespace Themosis\Core\Auth;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

trait VerifiesEmails
{
    use RedirectsUsers;

    /**
     * Show the email verification notice.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('auth.verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param Request $request
     *
     * @throws AuthorizationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function verify(Request $request)
    {
        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException();
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    /**
     * Resend the email verification notification.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}
