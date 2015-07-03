<?php
namespace Themosis\PostType;

use Themosis\Action\Action;
use Themosis\Core\DataContainer;
use Themosis\Metabox\IMetabox;
use Themosis\View\IRenderable;

class PostTypeBuilder implements IPostType
{
    /**
     * PostTypeData instance.
     *
     * @var \Themosis\Core\DataContainer
     */
    protected $datas;

    /**
     * Event object.
     */
    protected $event;

    /**
     * The registered custom post type.
     *
     * @var Object|\WP_Error
     */
    protected $postType;

    /**
     * The custom statuses.
     *
     * @var array
     */
    protected $status = [];

    /**
     * The custom publish metabox.
     *
     * @var IMetabox
     */
    protected $metabox;

    /**
     * The custom view used for publish metabox.
     * @var IRenderable
     */
    protected $view;

    /**
     * Build a custom post type.
     *
     * @param DataContainer $datas The post type properties.
     * @param IMetabox $metabox The custom metabox for custom publish metabox
     * @param IRenderable $view The view that handles custom publish metabox
     */
    public function __construct(DataContainer $datas, IMetabox $metabox, IRenderable $view)
    {
        $this->datas = $datas;
        $this->metabox = $metabox;
        $this->view = $view;
        $this->event = Action::listen('init', $this, 'register');
    }

    /**
     * Define a new custom post type.
     *
     * @param string $name The post type slug name.
     * @param string $plural The post type plural name for display.
     * @param string $singular The post type singular name for display.
     * @throws PostTypeException
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function make($name, $plural, $singular)
    {
        $params = compact('name', 'plural', 'singular');

        foreach ($params as $key => $param)
        {
            if (!is_string($param))
            {
                throw new PostTypeException('Invalid custom post type parameter "'.$key.'". Accepts string only.');
            }
        }

        // Set main properties.
        $this->datas['name'] = $name;
        $this->datas['args'] = $this->setDefaultArguments($plural, $singular);

        return $this;
    }

    /**
     * Set the custom post type. A user can also override the
     * arguments by passing an array of custom post type arguments.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     * @param array $params The custom post type arguments.
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function set(array $params = [])
    {
        // Override custom post type arguments if given.
        $this->datas['args'] = array_merge($this->datas['args'], $params);

        // Trigger the init event in order to register the custom post type.
        $this->event->dispatch();

        return $this;
    }

    /**
     * Triggered by the 'init' action event.
     * Register a WordPress custom post type.
     *
     * @return void
     */
    public function register()
    {
        $this->postType = register_post_type($this->datas['name'], $this->datas['args']);

        // Register the status.
        if (!empty($this->status))
        {
            foreach ($this->status as $key => $args)
            {
                register_post_status($key, $args);
            }

            // Build custom publish metabox
            $this->metabox->make(__('Publish'), $this->datas['name'], [
                'id'        => 'themosisSubmitdiv',
                'context'   => 'side',
                'priority'  => 'core'
            ], $this->view)->set()->with([
                'statuses' => $this->status
            ]);
        }
    }

    /**
     * Returns the custom post type slug name.
     *
     * @deprecated
     * @return string
     */
    public function getSlug()
    {
        return $this->datas['name'];
    }

    /**
     * @param null $property
     * @return array
     * @throws PostTypeException
     */
    public function get($property = null)
    {
        $name = [
            'name'  => $this->datas['name']
        ];

        $properties = array_merge($name, $this->datas['args']);

        // If no property asked, return all defined properties.
        if (is_null($property) || empty($property))
        {
            return $properties;
        }

        // If property exists, return it.
        if (isset($properties[$property]))
        {
            return $properties[$property];
        }

        throw new PostTypeException("Property '{$property}' does not exist on the '{$properties['name']}' custom post type.");
    }

    /**
     * Allow a user to change the title placeholder text.
     *
     * @param string $title The title placeholder text.
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function setTitle($title)
    {
        $name = $this->datas['name'];

        add_filter('enter_title_here', function($default) use($name, $title)
        {
            $screen = get_current_screen();

            if ($name == $screen->post_type)
            {
                $default = $title;
            }

            return $default;
        });

        return $this;
    }

    /**
     * Add custom post type status.
     *
     * @param array|string $status The status key name.
     * @param array $args The status arguments.
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function status($status, array $args = [])
    {
        // Allow multiple statuses...
        if (is_array($status))
        {
            foreach ($status as $key => $params)
            {
                if (is_int($key))
                {
                    $this->status($params);
                }
                elseif (is_string($key) && is_array($params))
                {
                    $this->status($key, $params);
                }
            }

            return $this;
        }

        // Set default arguments
        $defaultName = ucfirst($status);
        $args = wp_parse_args($args, [
            'label'                     => $defaultName,
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop($defaultName.' <span class="count">(%s)</span>', $defaultName.' <span class="count">(%s)</span>'),
            'publish_text'              => __('Apply Changes')
        ]);

        // Register the status
        $this->status[$status] = $args;

        // Remove default publish box
        add_action('add_meta_boxes', [$this, 'removeDefaultPublishBox']);

        // Apply selected status on save.
        add_filter('pre_post_status', [$this, 'applyStatus']);
        add_filter('status_save_pre', [$this, 'applyStatus']);

        return $this;
    }

    public function removeDefaultPublishBox()
    {
        // Remove current publish box
        remove_meta_box('submitdiv', $this->datas['name'], 'side');
    }

    /**
     * Apply the selected status to the post on save.
     *
     * @param string $value The translated value by WordPress (is always "publish" for some reasons.)
     * @return mixed
     */
    public function applyStatus($value)
    {
        // Check post_type and look if there are any custom statuses defined.
        if (isset($_POST['post_type']) && $this->datas['name'] === $_POST['post_type'] && !empty($this->status))
        {
            if ((isset($_POST['post_status']) && 'publish' === $_POST['post_status']) && (isset($_REQUEST['post_status']) && 'draft' === $_REQUEST['post_status']))
            {
                // New post with draft as default and "publish" button is clicked. Set to 1st registered post status.
                $statuses = array_keys($this->status);
                return esc_attr($statuses[0]);
            }
            elseif (isset($_REQUEST['post_status']) && !empty($_REQUEST['post_status']))
            {
                // Else simply apply the selected custom status.
                return esc_attr($_REQUEST['post_status']);
            }
        }

        return esc_attr($value);
    }

    /**
     * Set the custom post type default arguments.
     *
     * @param string $plural The post type plural display name.
     * @param string $singular The post type singular display name.
     * @return array
     */
    protected function setDefaultArguments($plural, $singular)
    {
        $labels = [
            'name'                  => __($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'singular_name'         => __($singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'add_new'               => __('Add New', THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'add_new_item'          => __('Add New '. $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'edit_item'             => __('Edit '. $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'new_item'              => __('New ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'all_items'             => __('All ' . $plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'view_item'             => __('View ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'search_items'          => __('Search ' . $singular, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'not_found'             =>  __('No '. $singular .' found', THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'not_found_in_trash'    => __('No '. $singular .' found in Trash', THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'parent_item_colon'     => '',
            'menu_name'             => __($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN)
        ];

        $defaults = [
            'label' 		=> __($plural, THEMOSIS_FRAMEWORK_TEXTDOMAIN),
            'labels' 		=> $labels,
            'description'	=> '',
            'public'		=> true,
            'menu_position'	=> 20,
            'has_archive'	=> true
        ];

        return $defaults;
    }

} 