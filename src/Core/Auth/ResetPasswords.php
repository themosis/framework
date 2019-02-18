<?php

namespace Themosis\Core\Auth;

use App\Forms\Auth\Password\PasswordResetForm;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Themosis\Forms\Contracts\FormInterface;

trait ResetPasswords
{
    use RedirectsUsers;

    /**
     * The full name of the form token field.
     *
     * @var string
     */
    protected $token_field = 'th_token';

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param Request     $request
     * @param string|null $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $form = new PasswordResetForm();
        $form->setToken($token);

        return view('auth.passwords.reset')->with([
            'form' => $this->form($form)
        ]);
    }

    /**
     * Handle user password reset request.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $resetForm = new PasswordResetForm();
        $resetForm->setToken($request->get($this->token_field));

        $form = $this->form($resetForm);
        $form->handleRequest($request);

        if ($form->isNotValid()) {
            return back()->withErrors($form->errors()->all());
        }

        $response = $this->broker()->reset(
            $this->credentials($form),
            function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Return successful password reset response.
     *
     * @param Request $request
     * @param string  $response
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectPath())
            ->with('status', trans($response));
    }

    /**
     * Return failed password reset response.
     *
     * @param Request $request
     * @param string  $response
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return back()->withErrors([
                'email' => trans($response)
            ]);
    }

    /**
     * Return password broker expected values.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    protected function credentials(FormInterface $form): array
    {
        return [
            'email' => $form->repository()->getFieldByName('email')->getValue(),
            'password' => $form->repository()->getFieldByName('password')->getValue(),
            'password_confirmation' => $form->repository()->getFieldByName('password_confirmation')->getValue(),
            'token' => $form->repository()->getFieldByName('token')->getValue()
        ];
    }

    /**
     * Reset the user password.
     *
     * @param User   $user
     * @param string $password
     */
    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));

        $this->guard()->login($user);
    }

    /**
     * Return the guard instance used during a password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard()
    {
        return Auth::guard();
    }

    /**
     * Return the password broker used during a reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }
}
