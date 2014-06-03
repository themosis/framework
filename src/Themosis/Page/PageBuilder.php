<?php
namespace Themosis\Page;

use Themosis\Action\Action;
use Themosis\Core\DataContainer;
use Themosis\Core\Wrapper;
use Themosis\Core\WrapperView;
use Themosis\Validation\ValidationBuilder;

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

    private $pageEvent;

    /**
     * Build a Page instance.
     *
     * @param DataContainer $datas The page properties.
     * @param WrapperView $view The page view file.
     * @param ValidationBuilder $validator The page validator.
     */
    public function __construct(DataContainer $datas, WrapperView $view, ValidationBuilder $validator)
    {
        $this->datas = $datas;
        $this->view = $view;
        $this->validator = $validator;

        // Events
        $this->pageEvent = Action::listen('admin_menu', $this, 'build');
    }

    /**
     * @param string $slug The page slug name.
     * @param string $title The page display title.
     * @param string $parent The parent's page slug if a subpage.
     * @param WrapperView $view The page main view file.
     * @throws PageException
     * @return \Themosis\Page\PageBuilder
     */
    public function make($slug, $title, $parent = null, WrapperView $view = null)
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
            'position'      => 85
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
        $this->view->render();
    }

} 