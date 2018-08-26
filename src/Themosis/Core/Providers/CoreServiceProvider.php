<?php

namespace Themosis\Core\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\AggregateServiceProvider;

class CoreServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array
     */
    protected $providers = [
        FormRequestServiceProvider::class
    ];

    /**
     * Register the service provider.
     */
    public function register()
    {
        parent::register();

        $this->registerRequestValidate();
    }

    /**
     * Register the "validate" macro on the request.
     */
    public function registerRequestValidate()
    {
        Request::macro('validate', function (array $rules, ...$params) {
            validator()->validate($this->all(), $rules, ...$params);

            return $this->only(collect($rules)->keys()->map(function ($rule) {
                return str_contains($rule, '.') ? explode('.', $rule)[0] : $rule;
            })->unique()->toArray());
        });
    }

    /**
     * Publish core assets.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../../../dist' => web_path('dist')
        ], 'themosis');
    }
}
