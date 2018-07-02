<?php

namespace Themosis\Core\Http\Events;

class RequestHandled
{
    /**
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * @var \Illuminate\Http\Response
     */
    public $response;

    /**
     * RequestHandled constructor.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Http\Response $response
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
