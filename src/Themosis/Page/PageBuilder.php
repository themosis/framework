<?php
namespace Themosis\Page;

use Themosis\Action\Action;
use Themosis\Core\DataContainer;
use Themosis\Core\Wrapper;
use Themosis\Validation\ValidationBuilder;
use Themosis\View\IRenderable;

class PageBuilder extends Wrapper {

    /**
     * The page properties.
     *
     * @var DataContainer
     */
    private $datas;

    /**
     * The page view file.
     *
     * @var \Themosis\Core\WrapperView
     */
    private $view;

    /**
     * The page validator object.
     *
     * @var \Themosis\Validation\ValidationBuilder
     */
    private $validator;

    /**
     * The page install action.
     *
     * @var static
     */
    private $pageEvent;

    /**
     * The page sections.
     *
     * @var array
     */
    private $sections;

    /**
     * The settings install action.
     *
     * @var static
     */
    private $settingsEvent;

    /**
     * The page settings.
     *
     * @var array
     */
    private $settings;

    /**
     * Build a Page instance.
     *
     * @param DataContainer $datas The page properties.
     * @param IRenderable $view The page view file.
     * @param ValidationBuilder $validator The page validator.
     */
    public function __construct(DataContainer $datas, IRenderable $view, ValidationBuilder $validator)
    {
        $this->datas = $datas;
        $this->view = $view;
        $this->validator = $validator;

        // Events
        $this->pageEvent = Action::listen('admin_menu', $this, 'build');
        $this->settingsEvent = Action::listen('admin_init', $this, 'installSettings');
    }

    /**
     * @param string $slug The page slug name.
     * @param string $title The page display title.
     * @param string $parent The parent's page slug if a subpage.
     * @param IRenderable $view The page main view file.
     * @throws PageException
     * @return \Themosis\Page\PageBuilder
     */
    public function make($slug, $title, $parent = null, IRenderable $view = null)
    {
        $params = compact('slug', 'title');

        foreach($params as $name => $param){
            if(!is_string($param)){
                throw new PageException('Invalid page parameter "'.$name.'"');
            }
        }

        // Check the view file.
        if(!is_null($view)){
            $this->view = $view;
        }

        // Set the page properties.
        $this->datas['slug'] = $slug;
        $this->datas['title'] = $title;
        $this->datas['parent'] = $parent;
        $this->datas['args'] = array(
            'capability'    => 'manage_options',
            'icon'          => '',
            'position'      => 85,
            'tabs'          => true
        );

        return $this;
    }

    /**
     * Set the custom page. Allow user to override
     * the default page properties and add its own
     * properties.
     *
     * @param array $params
     * @return \Themosis\Page\PageBuilder
     */
    public function set(array $params = array())
    {
        $this->datas['args'] = array_merge($this->datas['args'], $params);

        // Trigger the 'admin_menu' event in order to register the page.
        $this->pageEvent->dispatch();

        return $this;
    }

    /**
     * Triggered by the 'admin_menu' action event.
     * Register/display the custom page in the WordPress admin.
     *
     * @return void
     */
    public function build()
    {
        if(!is_null($this->datas['parent'])){

            add_submenu_page($this->datas['parent'], $this->datas['title'], $this->datas['title'], $this->datas['args']['capability'], $this->datas['slug'], array($this, 'displayPage'));

        } else {

            add_menu_page($this->datas['title'], $this->datas['title'], $this->datas['args']['capability'], $this->datas['slug'], array($this, 'displayPage'), $this->datas['args']['icon'], $this->datas['args']['position']);

        }
    }

    /**
     * Triggered by the 'add_menu_page' or 'add_submenu_page'.
     *
     * @return void
     */
    public function displayPage()
    {
        // Share the page instance to the view.
        $this->with('__page', $this);

        echo($this->view->render());
    }

    /**
     * Return a page property value.
     *
     * @param string $property
     * @return mixed
     */
    public function get($property = null)
    {
         return (isset($this->datas[$property])) ? $this->datas[$property] : '';
    }

    /**
     * Allow a user to pass custom datas to
     * the page view instance.
     *
     * @param string|array $key
     * @param mixed $value
     * @return \Themosis\Page\PageBuilder
     */
    public function with($key, $value = null)
    {
        $this->view->with($key, $value);

        return $this;
    }

    /**
     * Add custom sections for your settings.
     *
     * @param array $sections
     * @return \Themosis\Page\PageBuilder
     */
    public function addSections(array $sections = array())
    {
        $this->sections = $sections;

        // Pass all registered sections to the page view.
        $this->with('__sections', $this->sections);
    }

    /**
     * Check if the page has sections.
     *
     * @return bool
     */
    public function hasSections()
    {
        return count($this->sections) ? true : false;
    }

    /**
     * Add settings to the page.
     *
     * @param array $settings The page settings.
     * @return \Themosis\Page\PageBuilder
     */
    public function addSettings(array $settings = array())
    {
        $this->settings = $settings;

        // Trigger the 'admin_init' action.
        $this->settingsEvent->dispatch();

        return $this;
    }

    /**
     * Triggered by the 'admin_init' action.
     * Perform the WordPress settings API.
     *
     * @return void
     */
    public function installSettings()
    {
        // 2 ways to register the settings
        // a - With sections
        // b - Without sections
    }

} 