<?php
namespace Themosis\Metabox;

use Themosis\Action\Action;
use Themosis\Core\DataContainer;
use Themosis\Core\IWrapper;
use Themosis\Core\WrapperView;
use Themosis\Session\Session;

class MetaboxBuilder implements IWrapper {

    /**
     * Metabox instance datas.
     *
     * @var array
     */
    private $datas;

    /**
     * The metabox view.
     *
     * @var
     */
    private $view;

    /**
     * The display/install event to listen to.
     */
    private $installEvent;

    /**
     * Build a metabox instance.
     *
     * @param DataContainer $datas The metabox properties.
     * @param \Themosis\Core\WrapperView $view The metabox default view.
     */
    public function __construct(DataContainer $datas, WrapperView $view)
    {
        $this->datas = $datas;
        $this->view = $view;
        $this->installEvent = Action::listen('add_meta_boxes', $this, 'display');
        Action::listen('save_post', $this, 'save')->dispatch();
    }

    /**
     * Set a new metabox.
     *
     * @param string $title The metabox title.
     * @param string $postType The metabox parent slug name.
     * @param array $options Metabox extra options.
     * @param \Themosis\Core\WrapperView $view The metabox view.
     * @return object
     */
    public function make($title, $postType, array $options = array(), WrapperView $view = null)
    {
        $this->datas['title'] = $title;
        $this->datas['postType'] = $postType;
        $this->datas['options'] = $this->parseOptions($options);

        if(!is_null($view)){
            $this->view = $view;
        }

        return $this;
    }

    /**
     * Build the set metabox.
     *
     * @return void
     */
    public function set()
    {
        $this->installEvent->dispatch();
    }

    /**
     * The wrapper display method.
     *
     * @return void
     */
    public function display()
    {
        $id = md5($this->datas['title']);

        // Fields are passed to the metabox $args parameter.
        add_meta_box($id, $this->datas['title'], array($this, 'build'), 'post', 'normal', 'core', array(
            'main' => array(
                '<p>My custom field</p>',
                '<p>Second custom field</p>'
            ),
            'sidebar'   => array(
                '<h1>Sidebar field 01</h1>',
                \Themosis\Facades\Form::text('m-field', 'Hello')
            )
        ));
    }

    /**
     * Call by "add_meta_box", build the HTML code.
     *
     * @param \WP_Post $post The WP_Post object.
     * @param array $datas The metabox $args and associated fields.
     * @return void
     */
    public function build($post, array $datas)
    {
        // Add nonce fields
        wp_nonce_field(Session::nonceAction, Session::nonceName);

        // Build all the html with the fields
        // Place the fields at the right section
        foreach($this->view->getSections() as $section){

            foreach($datas['args'][$section] as $field){

                // Only pass the calculated field view.
                $this->view->fillSection($section, $field);

            }

        }

        // Render the full content.
        $this->view->render();
    }

    /**
     * The wrapper install method. Save container values.
     *
     * @return void
     */
    public function save()
    {
        // TODO: Implement save() method.
    }

    /**
     * Check metabox options: context, priority.
     *
     * @param array $options The metabox options.
     * @return array
     */
    private function parseOptions(array $options)
    {
        $newOptions = array();

        $allowed = array('context', 'priority');

        foreach ($options as $param => $value) {

            if (in_array($param, $allowed)) {

                $newOptions[$param] = $value;

            }

        }

        return $newOptions;

    }
}