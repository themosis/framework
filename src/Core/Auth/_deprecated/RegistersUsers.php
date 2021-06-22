<?php

namespace Themosis\Core\Auth;

use App\Forms\Auth\RegisterForm;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $form = $this->form(new RegisterForm());

        return view('auth.register', [
            'form' => $form
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function register(Request $request)
    {
        $form = $this->form(new RegisterForm());
        $form->handleRequest($request);

        if ($form->isValid()) {
            event(new Registered($customer = $this->create($form)));
            $this->guard()->login($customer);

            return $this->registered($request, $customer)
                ?: redirect($this->redirectPath());
        }

        return view('auth.register', [
            'form' => $form
        ]);
    }

    /**
     * Get the guard used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param Request $request
     * @param mixed   $customer
     */
    protected function registered(Request $request, $customer)
    {
        //
    }
}
