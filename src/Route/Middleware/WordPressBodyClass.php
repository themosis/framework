<?php

namespace Themosis\Route\Middleware;

use Themosis\Hook\FilterBuilder;
use Themosis\Route\Route;

class WordPressBodyClass
{
    /**
     * @var FilterBuilder
     */
    protected $filter;

    public function __construct(FilterBuilder $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Handle incoming request.
     *
     * @param $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->filter->add(
            'body_class',
            $this->dispatchBodyClass($request->route())
        );

        return $next($request);
    }

    /**
     * Return the callback managing route body CSS classes.
     *
     * @param Route $route
     *
     * @return \Closure
     */
    protected function dispatchBodyClass(Route $route)
    {
        return function ($classes) use ($route) {
            if ($route->hasCondition()) {
                return $classes;
            }

            $tokens = array_filter(array_map(function ($token) use ($route) {
                switch ($type = $token[0]) {
                    case 'variable':
                        if (isset($token[3]) && $route->hasParameter($paramKey = $token[3])) {
                            $param = $route->parameter($paramKey);

                            return is_string($param) ? sprintf('%s-%s', $paramKey, sanitize_title($param)) : false;
                        }

                        return false;
                        break;
                    case 'text':
                        return sanitize_title($token[1]);
                        break;
                    default:
                        return false;
                }
            }, array_reverse($route->getCompiled()->getTokens())));

            if (! empty($tokens)) {
                return array_filter(array_merge($tokens, $classes), function ($class) {
                    return 'error404' !== $class;
                });
            }

            return $classes;
        };
    }
}
