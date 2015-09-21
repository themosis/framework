<?php
namespace Themosis\Configuration;

use Themosis\Facades\Field;
use Themosis\Facades\Metabox;

class Template
{
	/**
	 * A list of given templates.
     *
     * @var array
	*/
	protected $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

	/**
	 * Init the page template module.
	 *
	 * @return \Themosis\Configuration\Template
	*/
	public function make()
	{
		// Set an empty value for no templates.
		$templateNames = array_merge(['none' => __('None')], $this->names());

		// Build a select field
		Metabox::make(__('Template'), 'page', [
            'context'   => 'side',
            'priority'  => 'core',
            'id'        => 'themosisTemplate'
        ])->set([
			Field::select('_themosisPageTemplate', [$templateNames], ['title' => __('Name')])
		]);

        return $this;
	}

	/**
	 * Get the template names data and return them
	 *
	 * @return array An array of template names.
	 */
	protected function names()
	{
		$names = [];

		foreach ($this->data as $key => $value)
		{
			if (is_int($key))
			{
                $names[$value] = str_replace(['-', '_'], ' ', ucfirst(trim($value)));
			}
			else
			{
                $names[$key] = $value;
			}
		}

		return $names;
	}

}
