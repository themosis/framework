<?php

namespace Themosis\Core\Support\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    /**
     * Register the application's policies.
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    public function register()
    {
        //
    }

    /**
     * Return the policies defined by the provider.
     *
     * @return array
     */
    public function policies()
    {
        return $this->policies;
    }
}
