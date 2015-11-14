<?php
namespace Themosis\Core;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionParameter;

class Container implements ArrayAccess{

    /**
     * An array of the types that have been resolved.
     *
     * @var array
     */
    protected $resolved = [];

    /**
     * The container's bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * Loaded instances aka the builders. (FormBuilder,...)
     *
     * @var array
     */
    protected $instances = [];

    /**
     * The registered type aliases.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * All of the registered rebound callbacks.
     *
     * @var array
     */
    protected $reboundCallbacks = [];

    /**
     * All of the registered resolving callbacks.
     *
     * @var array
     */
    protected $resolvingCallbacks = [];

    /**
     * All of the global resolving callbacks.
     *
     * @var array
     */
    protected $globalResolvingCallbacks = [];

    /**
     * Register an existing instance as shared in the container.
     *
     * @param string|array $abstract The abstract class name (ex: request) or the class full name. This could be an array containing the class full name and its alias.
     * @param mixed $instance
     * @return void
     */
    public function instance($abstract, $instance)
    {
        // First, we will extract the alias from the abstract if it is an array so we
        // are using the correct name when binding the type. If we get an alias it
        // will be registered with the container so we can resolve it out later.
        if (is_array($abstract))
        {
            list($abstract, $alias) = $this->extractAlias($abstract);
            $this->alias($abstract, $alias);
        }

        unset($this->aliases[$abstract]);

        // We'll check to determine if this type has been bound before, and if it has
        // we will fire the rebound callbacks registered with the container and it
        // can be updated with consuming classes that have gotten resolved here.
        $bound = $this->bound($abstract);

        $this->instances[$abstract] = $instance;

        if ($bound)
        {
            $this->rebound($abstract);
        }
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param string $abstract
     * @return bool
     */
    public function bound($abstract)
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Fire the "rebound" callbacks for the given abstract type.
     *
     * @param string $abstract
     * @return void
     */
    protected function rebound($abstract)
    {
        $instance = $this->make($abstract);

        foreach ($this->getReboundCallbacks($abstract) as $callback)
        {
            call_user_func($callback, $this, $instance);
        }
    }

    /**
     * Get the rebound callbacks for a given type.
     *
     * @param string $abstract
     * @return array
     */
    protected function getReboundCallbacks($abstract)
    {
        if (isset($this->reboundCallbacks[$abstract]))
        {
            return $this->reboundCallbacks[$abstract];
        }

        return [];
    }

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make($abstract, $parameters = [])
    {
        $abstract = $this->getAlias($abstract);

        // If an instance of the type is currently being managed as a singleton we'll
        // just return an existing instance instead of instantiating new instances
        // so the developer can keep using the same objects instance every time.
        if (isset($this->instances[$abstract]))
        {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);

        // We're ready to instantiate an instance of the concrete type registered for
        // the binding. This will instantiate the types, as well as resolve any of
        // its "nested" dependencies recursively until all have gotten resolved.
        if ($this->isBuildable($concrete, $abstract))
        {
            $object = $this->build($concrete, $parameters);
        }
        else
        {
            $object = $this->make($concrete, $parameters);
        }

        // If the requested type is registered as a singleton we'll want to cache off
        // the instances in "memory" so we can return it later without creating an
        // entirely new instance of an object on each subsequent request for it.
        if ($this->isShared($abstract))
        {
            $this->instances[$abstract] = $object;
        }

        $this->fireResolvingCallbacks($abstract, $object);

        $this->resolved[$abstract] = true;

        return $object;
    }

    /**
     * Fire all of the resolving callbacks.
     *
     * @param string $abstract
     * @param mixed $object
     * @return void
     */
    protected function fireResolvingCallbacks($abstract, $object)
    {
        if (isset($this->resolvingCallbacks[$abstract]))
        {
            $this->fireCallbackArray($object, $this->resolvingCallbacks[$abstract]);
        }

        $this->fireCallbackArray($object, $this->globalResolvingCallbacks);
    }

    /**
     * Fire an array of callbacks with an object.
     *
     * @param mixed $object
     * @param array $callbacks
     */
    protected function fireCallbackArray($object, array $callbacks)
    {
        foreach ($callbacks as $callback)
        {
            call_user_func($callback, $object, $this);
        }
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
        if ( !isset($this->bindings[$abstract]))
        {
            if ($this->missingLeadingSlash($abstract) && isset($this->bindings['\\'.$abstract]))
            {
                $abstract = '\\'.$abstract;
            }

            return $abstract;
        }

        return $this->bindings[$abstract]['concrete'];
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
    public function build($concrete, $parameters = [])
    {
        // If the concrete type is actually a Closure, we will just execute it and
        // hand back the results of the functions, which allows functions to be
        // used as resolvers for more fine-tuned resolution of these objects.
        if ($concrete instanceof Closure)
        {
            return $concrete($this, $parameters);
        }

        $reflector = new ReflectionClass($concrete);

        // If the type is not instantiable, the developer is attempting to resolve
        // an abstract type such as an Interface of Abstract Class and there is
        // no binding registered for the abstractions so we need to bail out.
        if (!$reflector->isInstantiable())
        {
            $message = "Target [$concrete] is not instantiable.";
            throw new \Exception($message);
        }

        $constructor = $reflector->getConstructor();

        // If there are no constructors, that means there are no dependencies then
        // we can just resolve the instances of the objects right away, without
        // resolving any other types or dependencies out of these containers.
        if (is_null($constructor))
        {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        // Once we have all the constructor's parameters we can create each of the
        // dependency instances and then use the reflection instances to make a
        // new instance of this class, injecting the created dependencies in.
        $parameters = $this->keyParametersByArgument($dependencies, $parameters);

        $instances = $this->getDependencies($dependencies, $parameters);

        // Return the class instance.
        return $reflector->newInstanceArgs($instances);
    }

    /**
     * Register a binding with the container.
     *
     * @param string|array $abstract
     * @param \Closure|string|null $concrete
     * @param bool $shared
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        // If the given types are actually an array, we will assume an alias is being
        // defined and will grab this "real" abstract class name and register this
        // alias with the container so that it can be used as a shortcut for it.
        if (is_array($abstract))
        {
            list($abstract, $alias) = $this->extractAlias($abstract);
            $this->alias($abstract, $alias);
        }

        // If no concrete type was given, we will simply set the concrete type to the
        // abstract type. This will allow concrete type to be registered as shared
        // without being forced to state their classes in both of the parameter.
        $this->dropStaleInstances($abstract);

        if (is_null($concrete))
        {
            $concrete = $abstract;
        }

        // If the factory is not a Closure, it means it is just a class name which
        // is bound into this container to the abstract type and we will just wrap
        // it up inside a Closure to make things more convenient when extending.
        if ( ! $concrete instanceof Closure)
        {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');

        // If the abstract type was already resolved in this container we'll fire the
        // rebound listener so that any objects which have already gotten resolved
        // can have their copy of the object updated via the listener callbacks.
        if ($this->resolved($abstract))
        {
            $this->rebound($abstract);
        }
    }

    /**
     * Bind a shared Closure into the container.
     *
     * @param string $abstract
     * @param \Closure $closure
     * @return void
     */
    public function bindShared($abstract, Closure $closure)
    {
        $this->bind($abstract, $this->share($closure), true);
    }

    /**
     * Wrap a Closure such that it is shared.
     *
     * @param \Closure $closure
     * @return \Closure
     */
    public function share(Closure $closure)
    {
        return function($container) use ($closure)
        {
            // We'll simply declare a static variable within the Closures and if it has
            // not been set we will execute the given Closures to resolve this value
            // and return it back to these consumers of the method as an instance.
            static $object;

            if (is_null($object))
            {
                $object = $closure($container);
            }

            return $object;
        };
    }

    /**
     * Determine if a given type is shared.
     *
     * @param string $abstract
     * @return bool
     */
    public function isShared($abstract)
    {
        if (isset($this->bindings[$abstract]['shared']))
        {
            $shared = $this->bindings[$abstract]['shared'];
        }
        else
        {
            $shared = false;
        }

        return isset($this->instances[$abstract]) || $shared === true;
    }

    /**
     * Get the Closure to be used when building a type.
     *
     * @param string $abstract
     * @param string $concrete
     * @return \Closure
     */
    protected function getClosure($abstract, $concrete)
    {
        return function($c, $parameters = []) use ($abstract, $concrete)
        {
            $method = ($abstract == $concrete) ? 'build' : 'make';
            return $c->$method($concrete, $parameters);
        };
    }

    /**
     * Drop all of the stale instances and aliases.
     *
     * @param string $abstract
     * @return void
     */
    protected function dropStaleInstances($abstract)
    {
        unset($this->instances[$abstract], $this->aliases[$abstract]);
    }

    /**
     * Determine if the given abstract type has been resolved.
     *
     * @param string $abstract
     * @return bool
     */
    public function resolved($abstract)
    {
        return isset($this->resolved[$abstract]) || isset($this->instances[$abstract]);
    }

    /**
     * Alias a type to a shorter name.
     *
     * @param string $abstract
     * @param string $alias
     * @return void
     */
    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;
    }

    /**
     * Extract the type and alias from a given definition.
     *
     * @param array $definition
     * @return array
     */
    protected function extractAlias(array $definition)
    {
        return array(key($definition), current($definition));
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
     * Get the alias for an abstract if available.
     *
     * @param string $abstract
     * @return string
     */
    protected function getAlias($abstract)
    {
        return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
    }

    /**
     * If extra parameters are passed by numeric ID, rekey them by argument name.
     *
     * @param array $dependencies
     * @param array $parameters
     * @return array
     */
    protected function keyParametersByArgument(array $dependencies, array $parameters)
    {
        foreach ($parameters as $key => $value)
        {
            if (is_numeric($key))
            {
                unset($parameters[$key]);
                $parameters[$dependencies[$key]->name] = $value;
            }
        }

        return $parameters;
    }

    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param array $parameters
     * @param array $primitives
     * @return array
     */
    protected function getDependencies($parameters, array $primitives = array())
    {
        $dependencies = [];

        foreach ($parameters as $parameter)
        {
            $dependency = $parameter->getClass();

            // If the class is null, it means the dependency is a string or some other
            // primitive type which we can not resolve since it is not a class and
            // we will just bomb out with an error since we have no-where to go.
            if (array_key_exists($parameter->name, $primitives))
            {
                $dependencies[] = $primitives[$parameter->name];
            }
            elseif (is_null($dependency))
            {
                $dependencies[] = $this->resolveNonClass($parameter);
            }
            else
            {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return (array) $dependencies;
    }

    /**
     * Resolve a non-class hinted dependency.
     *
     * @param \ReflectionParameter  $parameter
     * @return mixed
     *
     * @throws \Exception
     */
    protected function resolveNonClass(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable())
        {
            return $parameter->getDefaultValue();
        }

        $message = "Unresolvable dependency resolving [$parameter] in class {$parameter->getDeclaringClass()->getName()}";

        throw new \Exception($message);
    }

    /**
     * Resolve a class based dependency from the container.
     *
     * @param \ReflectionParameter  $parameter
     * @return mixed
     *
     * @throws \Exception
     */
    protected function resolveClass(ReflectionParameter $parameter)
    {
        try
        {
            return $this->make($parameter->getClass()->name);
        }

        // If we can not resolve the class instance, we will check to see if the value
        // is optional, and if it is we will return the optional parameter value as
        // the value of the dependency, similarly to how we do this with scalars.
        catch (\Exception $e)
        {
            if ($parameter->isOptional())
            {
                return $parameter->getDefaultValue();
            }

            throw $e;
        }
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
        return isset($this->bindings[$key]);
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
        return $this->make($key);
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
        // If the value is not a Closure, we will make it one. This simply gives
        // more "drop-in" replacement functionality for the Pimple which this
        // container's simplest functions are base modeled and built after.
        if (!$value instanceof Closure)
        {
            $value = function() use ($value)
            {
                return $value;
            };
        }

        $this->bind($key, $value);
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
        unset($this->bindings[$key], $this->instances[$key]);
    }

    /**
     * Dynamically access container services.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * Dynamically set container services.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this[$key] = $value;
    }
}