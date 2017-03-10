<?php

namespace Themosis\Config;

use Illuminate\Support\Arr;

class ConfigFactory implements IConfig, \ArrayAccess
{
    /**
     * Config file finder instance.
     *
     * @var ConfigFinder
     */
    protected $finder;

	/**
	 * All of the configuration items.
	 *
	 * @var array
	 */
	protected $items = [];


	public function __construct(ConfigFinder $finder)
    {
        $this->finder = $finder;
    }

	/**
	 * Return all or specific property from a config file.
	 *
	 * @param string $name The config file name or its property full name.
	 *
	 * @return mixed
	 */
	public function get( $name ) {

		if ( strpos( $name, '.' ) !== false ) {

			list( $name, $property ) = explode( '.', $name );
		}

		if ( $this->has( $name ) ) {

			$properties = Arr::get( $this->items, $name );

		} else {

			$path       = $this->finder->find( $name );
			$properties = include $path;

			$this->set( $name, $properties );
		}

		// Looking for single property
		if ( isset( $property ) && isset( $properties[ $property ] ) ) {

			return $properties[ $property ];
		}

		return $properties;
	}

	/**
	 * Determine if the given configuration value exists.
	 *
	 * @param  string $key
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return Arr::has( $this->items, $key );
	}


	/**
	 * Set a given configuration value.
	 *
	 * @param  array|string $key
	 * @param  mixed $value
	 *
	 * @return void
	 */
	public function set( $key, $value = null ) {
		if ( is_array( $key ) ) {
			foreach ( $key as $innerKey => $innerValue ) {
				Arr::set( $this->items, $innerKey, $innerValue );
			}
		} else {
			Arr::set( $this->items, $key, $value );
		}
	}

	/**
	 * Prepend a value onto an array configuration value.
	 *
	 * @param  string $key
	 * @param  mixed $value
	 *
	 * @return void
	 */
	public function prepend( $key, $value ) {
		$array = $this->get( $key );

		array_unshift( $array, $value );

		$this->set( $key, $array );
	}

	/**
	 * Push a value onto an array configuration value.
	 *
	 * @param  string $key
	 * @param  mixed $value
	 *
	 * @return void
	 */
	public function push( $key, $value ) {
		$array = $this->get( $key );

		$array[] = $value;

		$this->set( $key, $array );
	}

	/**
	 * Get all of the configuration items for the application.
	 *
	 * @return array
	 */
	public function all() {
		return $this->items;
	}


	/**
	 * Determine if the given configuration option exists.
	 *
	 * @param  string $key
	 *
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return $this->has( $key );
	}

	/**
	 * Get a configuration option.
	 *
	 * @param  string $key
	 *
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return $this->get( $key );
	}

	/**
	 * Set a configuration option.
	 *
	 * @param  string $key
	 * @param  mixed $value
	 *
	 * @return void
	 */
	public function offsetSet( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Unset a configuration option.
	 *
	 * @param  string $key
	 *
	 * @return void
	 */
	public function offsetUnset( $key ) {
		$this->set( $key, null );
	}

}
