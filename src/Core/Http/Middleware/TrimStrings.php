<?php

namespace Themosis\Core\Http\Middleware;

use Closure;

class TrimStrings extends TransformsRequest
{
    /**
     * All of the registered skip callbacks.
     *
     * @var array
     */
    protected static $skipCallbacks = [];

    /**
     * The attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Register a callback that instructs the middleware to be skipped.
     */
    public static function skipWhen(Closure $callback)
    {
        static::$skipCallbacks[] = $callback;
    }

    public function handle($request, Closure $next)
    {
        foreach (static::$skipCallbacks as $callback) {
            if ($callback($request)) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (in_array($key, $this->except, true)) {
            return $value;
        }

        return is_string($value) ? trim($value) : $value;
    }
}
