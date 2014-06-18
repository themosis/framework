<?php
namespace Themosis\Core;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest {

    /**
     * Return the Request instance.
     *
     * @return \Themosis\Core\Request
     */
    public function instance()
    {
        return $this;
    }

} 