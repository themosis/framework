<?php
namespace Themosis\Page;

defined('DS') or die('No direct script access.');

class PageData
{
	/**
	 * All page datas
	*/
	private $datas = array();

	public function __construct($params)
	{
		$this->datas = $params;
		$this->datas['sections'] = $this->parseSections($this->datas['sections']);
		$this->datas['settings'] = $this->parse($this->datas['settings']);
	}

	/**
	 * Parse the sections
	 * 
	 * @param array
	 * @return array
	*/
	private function parseSections($sections)
	{
		$newSections = array();

		foreach ($sections as $section) {
			
			$defaults = array(
				'name'		=> 'defaultSection',
				'title'		=> ucfirst($section['name']),
				'type'		=> 'section'
			);

			$newSections[] = wp_parse_args($section, $defaults);

		}

		return $newSections;
	}

	/**
	 * Parse the settings
	 * 
	 * @param array
	 * @return array
	*/
	private function parse($settings)
	{
		$newSettings = array();

		foreach ($settings as $setting) {
			
			$defaults = array(
				'name'      => 'default_field',
				'title'     => ucfirst($setting['name']),
				'info'      => '',
				'default'   => '',
				'type'      => 'text',
				'section'   => '',
				'options'   => array(),
				'class'     => '',
				'multiple'	=> false,
				'fields'	=> array()
			);

			// Mix values from defaults and $args and then extract the results as $variables
			extract(wp_parse_args($setting, $defaults));

			$field_args = array(
				'type'      => $type,
				'name'      => $name,
				'info'      => $info,
				'default'   => $default,
				'options'   => $options,
				'label_for' => $name,
				'class'     => $class,
				'section'	=> $section,
				'title'		=> $title,
				'multiple'	=> $multiple,
				'fields'	=> $fields
			);

			// Add new settings
			$newSettings[] = $field_args;

		}

		return $newSettings;
	}

	/**
	 * Return the property
	 * 
	 * @param string
	 * @return string
	*/
	public function get($param)
	{
		if (array_key_exists($param, $this->datas)) {
			return $this->datas[$param];
		} else {
			throw new PageException("The requested data does not exists.");
		}
	}

	/**
	 * Sanitize setting before saving it to the DB
	 * 
	 * @param array
	 * @return array
	*/
	public function validate($input)
	{
		// Will be returned with sanitized values
		// and saved in the wp_options table.
		$validInput = array();
       
		// Grab the settings given by the developper
		// and switch by their type in order
		// to validate the settings.
		$settings = $this->get('settings');

		foreach ($settings as $setting) :
				
			switch ($setting['type']) {

				//
				// TEXT input
				//
				case 'text':
					// Check if value are set
					if (!isset($input[$setting['name']])) {
						$input[$setting['name']] = '';
					}

					// Now switch by their "class". Their class will
					// affect how we sanitize the values. The class is
					// also used for styling. It just serves our purpose here.
					switch ($setting['class']) {
						case 'nohtml':
							// Remove all html tags, line breaks, white space, tabs...
							$validInput[$setting['name']] = htmlentities(sanitize_text_field($input[$setting['name']]));
							break;
						
						default:
							$allowed_html = array(
								'a'         => array(
								'href'      => array(),
								'title'     => array()
								),
								'b'         => array(),
								'em'        => array(),
								'i'         => array(),
								'strong'    => array()
							);
							$validInput[$setting['name']] = wp_kses(force_balance_tags(htmlentities(trim($input[$setting['name']]))), $allowed_html);
							break;
					}
					break; // END "TEXT"

				//
				// TEXTAREA input
				//
				case 'textarea':

					// Check if value are set
					if (!isset($input[$setting['name']])) {
						$input[$setting['name']] = '';
					}

					switch ($setting['class']) {
						case 'nohtml':
							$validInput[$setting['name']] = htmlentities(sanitize_text_field($input[$setting['name']]));
							break;
						
						default:
							$allowed_html = array(
                                'a'             => array('href' => array (),'title' => array ()),
                                'b'             => array(),
                                'blockquote'    => array('cite' => array ()),
                                'br'            => array(),
                                'dd'            => array(),
                                'dl'            => array(),
                                'dt'            => array(),
                                'em'            => array(), 
                                'i'             => array(),
                                'li'            => array(),
                                'ol'            => array(),
                                'p'             => array(),
                                'q'             => array('cite' => array ()),
                                'strong'        => array(),
                                'ul'            => array(),
                                'h1'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
                                'h2'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
                                'h3'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
                                'h4'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
                                'h5'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),
                                'h6'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ())
                            );

                            $validInput[$setting['name']] = wp_kses(force_balance_tags(trim($input[$setting['name']])), $allowed_html);
							break;
					}

					break;

				//
				// CHECKBOX input
				//
				case 'checkbox':

					if (!isset($input[$setting['name']])) {
						$input[$setting['name']] = 'off';
					}

					$validInput[$setting['name']] = ($input[$setting['name']] === 'on') ? 'on' : 'off';
					break;

				//
				// CHECKBOXES input
				//
				case 'checkboxes':

					if (!isset($input[$setting['name']])) {
						$input[$setting['name']] = array();
					}

					$validInput[$setting['name']] = $input[$setting['name']];
					break;

				//
				// RADIO input
				//
				case 'radio':

					if (!isset($input[$setting['name']])) {
						$input[$setting['name']] = array();
					}

					$validInput[$setting['name']] = $input[$setting['name']];
					break;

				//
				// SELECT input
				//
				case 'select':

					if (!isset($input[$setting['name']])) {
						$input[$setting['name']] = array();
					}

					// VALUE may be a string or an array
					$validInput[$setting['name']] = $input[$setting['name']];
					break;

				//
				// MEDIA input
				//
				case 'media':

					// Check if value are set
					if (!isset($input[$setting['name']])) {
						$input[$setting['name']] = '';
					}

					$validInput[$setting['name']] = htmlentities(sanitize_text_field($input[$setting['name']]));
					break;

				case 'infinite':

					if (!isset($input[$setting['name']])) {
						$input[$setting['name']] = array();
					}

					$validInput[$setting['name']] = $input[$setting['name']];
					break;
				
				default:
					$validInput[$setting['name']] = htmlentities(sanitize_text_field($input[$setting['name']]));
					break;
			} // END MAIN SWITCH

		endforeach;

		return $validInput;

	}
}