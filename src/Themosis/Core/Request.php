<?php
namespace Themosis\Core;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

class Request extends SymfonyRequest {

    /**
     * JSON content.
     *
     * @var string
     */
    protected $json;

    /**
     * Return the Request instance.
     *
     * @return \Themosis\Core\Request
     */
    public function instance()
    {
        return $this;
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        $input = $this->getInputSource()->all() + $this->query->all();

        return array_get($input, $key, $default);
    }

    /**
     * Get the input source for the request.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson())
        {
            return $this->json();
        }

        return $this->getMethod() == 'GET' ? $this->query : $this->request;
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return str_contains($this->header('CONTENT_TYPE'), '/json');
    }

    /**
     * Get the JSON payload for the request.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        if (!isset($this->json))
        {
            $this->json = new ParameterBag((array) json_decode($this->getContent(), true));
        }

        if (is_null($key))
        {
            return $this->json;
        }

        return array_get($this->json->all(), $key, $default);
    }

    /**
     * Retrieve a header from the request.
     *
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public function header($key = null, $default = null)
    {
        return $this->retrieveItem('headers', $key, $default);
    }

    /**
     * Retrieve a parameter item from a given source.
     *
     * @param string $source
     * @param string $key
     * @param mixed $default
     * @return string
     */
    protected function retrieveItem($source, $key, $default)
    {
        if (is_null($key))
        {
            return $this->$source->all();
        }
        else
        {
            return $this->$source->get($key, $default, true);
        }
    }

    /**
     * Create a new themosis Request from a request instance.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Themosis\Core\Request
     */
    public static function createFromBase(SymfonyRequest $request)
    {
        if ($request instanceof static) return $request;

        $r = new static();

        return $r->duplicate($request->query->all(), $request->request->all(), $request->attributes->all(), $request->cookies->all(), $request->files->all(), $request->server->all());
    }

} 