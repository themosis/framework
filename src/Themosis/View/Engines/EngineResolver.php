<?php
namespace Themosis\View\Engines;

use Closure;

class EngineResolver {

    /**
     * List of engines.
     *
     * @var array
     */
    protected $resolvers = array();

    /**
     * List of engine instances.
     *
     * @var array
     */
    protected $resolved = array();

    /**
     * Register an engine resolver.
     *
     * @param string $engine The engine name.
     * @param callable $resolver
     * @return void
     */
    public function register($engine, Closure $resolver)
    {
        $this->resolvers[$engine] = $resolver;
    }

    /**
     * Fetch an engine instance by name.
     *
     * @param string $engine The engine name.
     * @return \Themosis\View\Engines\IEngine
     */
    public function resolve($engine)
    {
        if(!isset($this->resolved[$engine])){
            $this->resolved[$engine] = call_user_func($this->resolvers[$engine]);
        }

        return $this->resolved[$engine];
    }

} 