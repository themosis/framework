<?php
namespace Themosis\Metabox;

use Themosis\Action\Action;

defined('DS') or die('No direct script access.');

class Metabox
{

	/**
	 * The metabox title
	*/
	private $title;

	/**
	 * The post type slug in which the metabox is.
	*/
	private $postType;

	/**
	 * The metabox data object
	*/
	private $data;

	/**
	 * Event that handle the installation
	*/
	private $installEvent;

	/**
	 * The post object in which the metabox belongs to
	*/
	private $post;

	/**
	 * Metabox parser
	*/
	private $parser;

	/**
	 * Optional parameters
	*/
	private $options = array();

	/**
	 * Allowed parameters
	*/
	private $allowedOptions = array('context', 'priority');

	/**
	 * Initialize each metabox and register its hooks.
	*/
	public function __construct($title, $postType, $options = array())
	{
		$this->title = $title;
		$this->postType = $postType;
		$this->options = $this->parseOptions($options);

		$this->data = new MetaboxData();
		$this->parser = new MetaboxParser($this->data);

		$this->installEvent = Action::listen('add_meta_boxes', $this, 'install');
		Action::listen('save_post', $this, 'save')->dispatch();
	}

    /**
     * Build a new metabox.
     * Pass its title and post type in which the metabox should
     * appear.
     * Optional parameters can be passed in an array. Parameters like
     * 'context', 'priority'
     *
     * @param $title
     * @param $postType
     * @param array $options
     * @throws MetaboxException
     * @return static
     * @internal param $string
     * @internal param $string
     * @internal param $array
     */
	public static function make($title, $postType, $options = array()){

		if (is_string($title) && is_string($postType)) {

			return new static($title, $postType, $options);

		} else {
			throw new MetaboxException("Invalid parameters. Enable to build the metabox.");
		}

	}

    /**
     * Trigger everything. Set the fields of the metabox.
     *
     * @param array
     * @throws MetaboxException
     */
	public function set($datas)
	{
		if (is_array($datas) && !empty($datas)) {
			$this->data->set($datas);

			// Now the datas are saved.
			// Trigger the installation of the metabox.
			$this->installEvent->dispatch();

		} else {
			throw new MetaboxException("Invalid metabox datas. Accepts only an array.");
		}
	}

	/**
	 * Handle the Metabox installation
	*/
	public function install()
	{
		$id = md5($this->title);
		$context = (isset($this->options['context'])) ? $this->options['context'] : 'normal';
		$priority = (isset($this->options['priority'])) ? $this->options['priority'] : 'high';

		add_meta_box($id, $this->title, array(&$this, 'build'), $this->postType, $context, $priority, $this->data->get());
	}

	/**
	 * Launch a MetaboxRenderer object. Handle the display
	 * of the metabox.
	*/
	public function build($post, $datas)
	{
		$this->post = $post;

		return MetaboxRenderer::render($this->post, $datas);
	}

	/**
	 * Save metabox datas
	*/
	public function save($postId)
	{
		$this->parser->save($this->data->get(), $postId);
	}

	/**
	 * Set a user capability check.
	 * 
	 * @param string
	 * @param int
	 * @param mixed (optional)
	*/
	public function userCap($cap, $userId = null, $args = null)
	{
		if (is_string($cap) && !empty($cap)) {
			$this->parser->setType($this->postType);
			$this->parser->userCheck($cap, $userId, $args);	
		}
	}

	/**
	 * Parse the given optionals parameters
	 * 
	 * @param array
	 * @return array
	*/
	private function parseOptions($options)
	{
		$newOptions = array();

		if (is_array($options) && !empty($options)) {
			
			foreach ($options as $param => $value) {
				
				if (in_array($param, $this->allowedOptions)) {
					
					$newOptions[$param] = $value;

				}

			}

			return $newOptions;

		}
		
	}
}