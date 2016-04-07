<?php

namespace Themosis\Route\Matching;

use Illuminate\Http\Request;
use Themosis\Route\Route;

class TemplateValidator implements ValidatorInterface {

    /**
     * Validate a given rule against a route and request.
     *
     * @param  \Themosis\Route\Route $route
     * @param  \Illuminate\Http\Request $request
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        // Check if a template is associated and compare it to current route condition.
        if ($this->requestHasTemplate() && 'themosis_is_template' !== $route->condition()) {
            return false;
        }

        return true;
    }

    /**
     * Check if the current request is using a page template.
     *
     * @return bool
     */
    protected function requestHasTemplate()
    {
        $qo = get_queried_object();

        if (is_a($qo, 'WP_Post') && 'page' === $qo->post_type) {
            $template = \Meta::get($qo->ID, '_themosisPageTemplate');

            if ('none' !== $template && !empty($template)) {
                return true;
            }
        }

        return false;
    }

}
