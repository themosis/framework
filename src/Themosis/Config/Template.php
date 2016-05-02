<?php

namespace Themosis\Config;

use Themosis\Hook\IHook;

class Template
{
    /**
     * A list of given templates.
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var IHook
     */
    protected $filter;

    public function __construct(array $data, IHook $filter)
    {
        $this->data = $data;
        $this->filter = $filter;
    }

    /**
     * Init the page template module.
     *
     * @return \Themosis\Config\Template
     */
    public function make()
    {
        // Set an empty value for no templates.
        $templates = $this->names();

        $this->filter->add('theme_page_templates', function($registeredTemplates) use ($templates)
        {
            return array_merge($registeredTemplates, $templates);
        });

        return $this;
    }

    /**
     * Get the template names data and return them.
     *
     * @return array An array of template names.
     */
    protected function names()
    {
        $names = [];

        foreach ($this->data as $key => $value) {
            if (is_int($key)) {
                $names[$value] = str_replace(['-', '_'], ' ', ucfirst(trim($value)));
            } else {
                $names[$key] = $value;
            }
        }

        return $names;
    }
}
