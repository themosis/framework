<?php
namespace Themosis\Core;

use Closure;
use ArrayAccess;
use ReflectionClass;

abstract class Container implements ArrayAccess{

    /**
     * Available igniters to the application.
     *
     * @var array
     */
    protected $igniters = array();

    /**
     * Loaded instances aka the builders. (FormBuilder,...)
     *
     * @var array
     */
    protected $instances = array();

    /**
     * Register an existing instance as shared in the container.
     *
     * @param string $abstract
     * @param mixed $instance
     * @return void
     */
    protected function instance($abstract, $instance)
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve the given type from the container.
     * At the moment, only triggered by Controller classes.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make($abstract, $parameters = array())
    {
        // If an instance of the type is currently being managed as a singleton we'll
        // just return an existing instance instead of instantiating new instances
        // so the developer can keep using the same objects instance every time.
        if(isset($this->instances[$abstract])){

            return $this->instances[$abstract];

        }

        $concrete = $this->getConcrete($abstract);

        // We're ready to instantiate an instance of the concrete type registered for
        // the binding. This will instantiate the types, as well as resolve any of
        // its "nested" dependencies recursively until all have gotten resolved.
        if($this->isBuildable($concrete, $abstract)){

            $object = $this->build($concrete, $parameters);

        } else {

            $object = $this->make($concrete, $parameters);

        }

        return $object;
    }

    /**
     * Get the concrete type for a given abstract.
     *
     * @param string $abstract
     * @return mixed $concrete
     */
    protected function getConcrete($abstract)
    {
        // If we don't have a registered resolver or concrete for the type, we'll just
        // assume each type is a concrete name and will attempt to resolve it as is
        // since the container should be able to resolve concretes automatically.
        if (!isset($this->igniters[$abstract])){

            if($this->missingLeadingSlash($abstract) && isset($this->igniters['\\'.$abstract])){

                $abstract = '\\'.$abstract;

            }

            return $abstract;

        } else {

            return $this->igniters[$abstract]['concrete'];

        }
    }

    /**
     * Determine if the given abstract has a leading slash.
     *
     * @param string $abstract
     * @return bool
     */
    protected function missingLeadingSlash($abstract)
    {
        return is_string($abstract) && strpos($abstract, '\\') !== 0;
    }

    /**
     * Determine if the given concrete is buildable.
     *
     * @param mixed $concrete String or Closure.
     * @param string $abstract
     * @return bool
     */
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * Instantiate a concrete instance of the given type.
     * At the moment, only 'controller' classes are using this method.
     *
     * @param string $concrete
     * @param array $parameters
     * @return mixed
     * @throws \Exception
     */
    public function build($concrete, $parameters = array())
    {
        // If the concrete type is actually a Closure, we will just execute it and
        // hand back the results of the functions, which allows functions to be
        // used as resolvers for more fine-tuned resolution of these objects.
        if($concrete instanceof Closure){

            return $concrete($this, $parameters);

        }

        $reflector = new ReflectionClass($concrete);

        // If the type is not instantiable, the developer is attempting to resolve
        // an abstract type such as an Interface of Abstract Class and there is
        // no binding registered for the abstractions so we need to bail out.
        if(!$reflector->isInstantiable()){

            $message = "Target [$concrete] is not instantiable.";

            throw new \Exception($message);
        }

        // Return the class instance.
        return new $concrete;

        //@TODO Allow called class to have dependencies...

    }

    /**
     * Retrieve the igniter class name.
     *
     * @param string $key The igniter key name.
     * @return string
     */
    public function getIgniter($key)
    {
        return $this->igniters[$key];
    }

    /**
     * Fire the igniterService.
     *
     * @param string $facadeKey The facade key name.
     * @return mixed
     */
    public function fire($facadeKey)
    {
        $igniter = $this->getIgniter($facadeKey);
        $service = new $igniter($this);
        $service->ignite();

        // Return the associated builder class instance.
        return $this[$facadeKey];
    }

    /**
     * An offset to check for. Run when used with isset()
     *
     * @link http://www.php.net/manual/fr/class.arrayaccess.php
     * @param string $key The key name of the igniter.
     * @return bool True on success or false on failure.
     */
    public function offsetExists($key)
    {
        return isset($this->instances[$key]);
    }

    /**
     * Instance to retrieve. Run when using $obj[$key]
     *
     * @link http://www.php.net/manual/fr/class.arrayaccess.php
     * @param string $key The key name of the igniter.
     * @return mixed
     */
    public function offsetGet($key)
    {
        // Check if $key is already registered in $instances
        // If not, create it.
        if(!isset($this->instances[$key])){

            $this->instances[$key] = $this->fire($key);

        }

        // If $key is registered, return it.
        return isset($this->instances[$key]) ? $this->instances[$key] : null;
    }

    /**
     * Add an instance to the list. Run when using $obj[$key] = $value
     *
     * @link http://www.php.net/manual/fr/class.arrayaccess.php
     * @param string $key The instance key name.
     * @param mixed $value The instance to add.
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key)) {
            $this->instances[] = $value;
        } else {
            $this->instances[$key] = $value;
        }
    }

    /**
     * Remove an instance of the list. Run when used by unset($obj[$key])
     *
     * @link http://www.php.net/manual/fr/class.arrayaccess.php
     * @param string $key The instance key name.
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->instances[$key]);
    }
}