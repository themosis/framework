<?php

namespace Themosis\Core\Auth;

use App\Forms\Auth\LoginForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Themosis\Forms\Contracts\FormInterface;

trait AuthenticatesUsers
{
    use ThrottlesLogins, RedirectsUsers;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('auth.login', [
            'form' => $this->form(new LoginForm())
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $form = $this->form(new LoginForm());
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($this->attemptLogin($request, $form)) {
                return $this->sendLoginResponse($request);
            }
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param Request $request
     *
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param Request       $request
     * @param FormInterface $form
     *
     * @return bool
     */
    protected function attemptLogin(Request $request, FormInterface $form)
    {
        return $this->guard()->attempt(
            [
                $this->username() => $form->repository()->getFieldByName($this->username())->getValue(),
                'password' => $form->repository()->getFieldByName('password')->getValue()
            ],
            $request->filled($this->remember())
        );
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed login response.
     *
     * @param Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')]
        ]);
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param $user
     *
     * @return bool
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }

    /**
     * Get the login username field to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Get the login remember field to be used by the controller.
     *
     * @return string
     */
    public function remember()
    {
        return 'th_remember';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
