<?php
namespace Themosis\Route;

class RouteData
{
	/**
	 * Route callback function
	*/
	private $callback;

	/**
	 * Route closure
	*/
	private $closure;

	/**
	 * Route callback terms
	*/
	private $terms = array();
	
	/**
	 * Route options
	 * 
	 * @var array
	*/
	private $options = array();
	
	/**
	 * Allowed options
	 *
	 * @var array
	*/
	private $allowedOptions = array('method', 'template', 'ssl');

	/**
	 * Route is using a template
     *
     * @deprecated
	*/
	private $template = false;

	/**
	 * Controller infos
	*/
	private $controller = array();

	public function __construct(array $datas)
	{	    
		$this->callback = $datas['callback'];
		$this->closure = (is_callable($datas['closure'])) ? $datas['closure'] : '';

		$controllerPath = (is_string($datas['closure']) && !is_callable($datas['closure'])) ? $datas['closure'] : '';
		$this->controller = $this->parseController($controllerPath);

		$this->terms = $datas['terms'];
		
		/*-----------------------------------------------------------------------*/
		// Check the options
		/*-----------------------------------------------------------------------*/
		$this->options = $this->parseOptions($datas['options']);
	}

    /**
     * Check and parse the route options.
     *
     * @param array $options The route options. By default the method receive an empty array
     * @throws RouteException
     * @return array Return default and/or defined options.
     */
	private function parseOptions(array $options)
	{
		$newOptions = array();
		
		/*-----------------------------------------------------------------------*/
		// First check if $options is an array... If not warn the user...
		/*-----------------------------------------------------------------------*/
		if (is_array($options)) {
    		
    		/*-----------------------------------------------------------------------*/
    		// Check if $options is not empty
    		/*-----------------------------------------------------------------------*/
    		if (0 < count($options)) {
        		
        		/*-----------------------------------------------------------------------*/
        		// Check if options are allowed.
        		/*-----------------------------------------------------------------------*/
        		foreach ($options as $key => $option) {
            		
            		if (in_array($key, $this->allowedOptions)) {
            		
            		    /*-----------------------------------------------------------------------*/
            		    // Clean value - This convert boolean values to string !
            		    /*-----------------------------------------------------------------------*/
            		    if (is_string($option)) trim($option);
                		
                		/*-----------------------------------------------------------------------*/
                		// Check if the option is 'method' -> parse the given value before adding
                		// the option.
                		/*-----------------------------------------------------------------------*/
                		if ('method' === $key) {
                    		
                    		if (!in_array(strtoupper($option), array('ANY', 'GET', 'POST'))) throw new RouteException('Wrong value given to the "method" option.');
                    		
                    		/*-----------------------------------------------------------------------*/
                    		// Then add the option to the $newOptions;
                    		// Make sure to capitalize the value for the 'method' option.
                    		/*-----------------------------------------------------------------------*/
                    		$newOptions[$key] = strtoupper($option);
                    		
                		} elseif ('ssl' === $key) {

                			if (!is_bool($option)) throw new RouteException('The SSL option only accepts boolean values.');
                		
                			$newOptions[$key] = $option;

                		} else {
                    		
                    	    /*-----------------------------------------------------------------------*/
                    		// Add the option to the $newOptions
                    		/*-----------------------------------------------------------------------*/
                    		$newOptions[$key] = $option;	
                    		
                		}
                		
            		} else {
                		
                		throw new RouteException('The given option is not valid. Please check documentation for allowed route options.');
                		
            		}
            		
        		}
        		
    		} else {
        		
        		/*-----------------------------------------------------------------------*/
        		// $options is empty so set the default values
        		// Set the 'method' option to 'ANY' so the route
        		// can handle any http methods
        		// Set 'ssl' to false to perform a default behaviour
        		/*-----------------------------------------------------------------------*/
        		$newOptions['method'] = 'ANY';
        		$newOptions['ssl'] = false;
        		
    		}
    		
        
            /*-----------------------------------------------------------------------*/
            // Return the $newOptions
            /*-----------------------------------------------------------------------*/
            return $newOptions;
        
    		
		} else {
    		
    		throw new RouteException('Invalid options parameter. An array is expected.');
    		
		}
		
	}

	/**
	 * Set info for the controller.
	 * 
	 * @param string $path The defined controller path.
	 * @return array The controller properties.
	 */
	private function parseController($path)
	{
		// Check if it's a "controller path" before processing.
		if (strpos($path, '@')) {

			$infos = explode('@', $path);

			$controllerDatas = array(
				$infos[0],
				ucfirst($infos[0]).'_Controller',
				$infos[1]
			);

			return $controllerDatas;

		}

		return array();
		
	}

	/**
	 * Return the WordPress conditional function signature.
	 * 
	 * @return string The core WordPress function signature.
	 */
	public function getCallback()
	{
		return $this->callback;
	}

	/**
	 * Return the WordPress conditional function terms.
	 * 
	 * @return array The conditional terms.
	 */
	public function getTerms()
	{
		return $this->terms;
	}

	/**
	 * Return the route closure object
	 * 
	 * @return object|string The closure object or empty string if no closure.
	 */
	public function getClosure()
	{
		return $this->closure;
	}

	/**
	 * Return a boolean. True when there is
	 * a registered template, false when no
	 * template assigned.
	 * 
	 * @return bool True. False if no template.
	 */
	public function getTemplate()
	{
	    if (isset($this->options['template']) && true === $this->options['template']) {
    	    
    	    return $this->options['template'];
    	    
	    }
	    
		return false;
	}

	/**
	 * Retrieve the controller info.
	 * 
	 * @return array The controller properties.
	 */
	public function getController()
	{
		return $this->controller;
	}
	
	/**
	 * Retrieve the method option.
	 *
	 * @return string The HTTP method value: 'ANY', 'GET', 'POST',...
	 */
	public function getMethod()
	{
		
		if (isset($this->options['method']) && !empty($this->options['method'])) {
    	    
    	    return $this->options['method'];
    	    	
		}
		
		/*-----------------------------------------------------------------------*/
		// By default, always return 'ANY' as method value.
		/*-----------------------------------------------------------------------*/
		return 'ANY';
		
	}

	/**
	 * Retrieve the 'ssl' option value.
     *
	 * @return bool False. True if ssl is defined.
	 */
	public function getSsl()
	{
		if (isset($this->options['ssl']) && $this->options['ssl']) {

			return $this->options['ssl'];

		}

		return false;
	}
}