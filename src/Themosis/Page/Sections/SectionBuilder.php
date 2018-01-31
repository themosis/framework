<?php

namespace Themosis\Page\Sections;

use Illuminate\View\View;
use Themosis\Foundation\DataContainer;

class SectionBuilder
{
    /**
     * Section data.
     *
     * @var \Themosis\Foundation\DataContainer
     */
    protected $data;

    /**
     * Section view.
     *
     * @var \Illuminate\View\View
     */
    protected $view;

    /**
     * Section custom datas.
     *
     * @var array
     */
    protected $shared = [];

    public function __construct(DataContainer $data)
    {
        $this->data = $data;
    }

    /**
     * Define a Section instance. Used in Page sections.
     *
     * @param string                $slug
     * @param string                $name
     * @param array                 $data Custom properties for the section.
     * @param \Illuminate\View\View $view
     *
     * @throws \Exception
     *
     * @return \Themosis\Page\Sections\SectionBuilder
     */
    public function make($slug, $name, array $data = [], View $view = null)
    {
        $params = compact('slug', 'name');

        foreach ($params as $var => $param) {
            if (!is_string($param)) {
                throw new \Exception('Invalid section parameter "'.$var.'"');
            }
        }

        $this->data['slug'] = $slug;
        $this->data['name'] = $name;
        $this->data['args'] = $data;

        if (!is_null($view)) {
            $this->view = $view;
        }

        return $this;
    }

    /**
     * Register custom data for the section view.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return \Themosis\Page\Sections\SectionBuilder
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->shared = array_merge($this->shared, $key);
        } else {
            $this->shared[$key] = $value;
        }

        return $this;
    }

    /**
     * Return the section datas.
     *
     * @return DataContainer
     */
    public function getData()
    {
        return $this->data;
    }
}
