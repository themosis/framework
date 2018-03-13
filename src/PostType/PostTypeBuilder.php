<?php

namespace Themosis\PostType;

use Illuminate\View\View;
use Themosis\Foundation\Application;
use Themosis\Foundation\DataContainer;
use Themosis\Hook\IHook;
use Themosis\Metabox\IMetabox;

class PostTypeBuilder implements IPostType
{
    /**
     * PostTypeData instance.
     *
     * @var \Themosis\Foundation\DataContainer
     */
    protected $datas;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var IHook
     */
    protected $filter;

    /**
     * The registered custom post type.
     *
     * @var object|\WP_Error
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
     *
     * @var \Illuminate\View\View
     */
    protected $view;

    /**
     * Application container.
     *
     * @var \Themosis\Foundation\Application
     */
    protected $container;

    /**
     * Instance abstract name prefix for registration into the container.
     *
     * @var string
     */
    protected $prefix = 'posttype';

    /**
     * Build a custom post type.
     *
     * @param Application           $container The application container.
     * @param DataContainer         $datas     The post type properties.
     * @param IMetabox              $metabox   The custom metabox for custom publish metabox
     * @param \Illuminate\View\View $view      The view that handles custom publish metabox
     * @param IHook                 $action    The action class
     * @param IHook                 $filter    The filter class
     */
    public function __construct(Application $container, DataContainer $datas, IMetabox $metabox, View $view, IHook $action, IHook $filter)
    {
        $this->container = $container;
        $this->datas = $datas;
        $this->metabox = $metabox;
        $this->view = $view;
        $this->action = $action;
        $this->filter = $filter;
    }

    /**
     * Define a new custom post type.
     *
     * @param string $name     The post type slug name.
     * @param string $plural   The post type plural name for display.
     * @param string $singular The post type singular name for display.
     *
     * @throws PostTypeException
     *
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function make($name, $plural, $singular)
    {
        $params = compact('name', 'plural', 'singular');

        foreach ($params as $key => $param) {
            if (!is_string($param)) {
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
     *
     * @param array $params The custom post type arguments.
     *
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function set(array $params = [])
    {
        // Override custom post type arguments if given.
        $this->datas['args'] = array_replace_recursive($this->datas['args'], $params);

        // Trigger the init event in order to register the custom post type.
        // Check if we are not already called by a method attached to the `init` hook.
        $current = current_filter();

        if ('init' === $current) {
            // If inside an `init` action, simply call the register method.
            $this->register();
        } else {
            // Out of an `init` action, call the hook.
            $this->action->add('init', [$this, 'register']);
        }

        // Register each custom post type instances into the container.
        $this->container->instance($this->prefix.'.'.$this->datas['name'], $this);

        return $this;
    }

    /**
     * Triggered by the 'init' action event.
     * Register a WordPress custom post type.
     */
    public function register()
    {
        $this->postType = register_post_type($this->datas['name'], $this->datas['args']);

        // Register the status.
        if (!empty($this->status)) {
            foreach ($this->status as $key => $args) {
                register_post_status($key, $args);
            }

            // Build custom publish metabox
            $this->metabox->make(__('Publish'), $this->datas['name'], [
                'id' => 'themosisSubmitdiv',
                'context' => 'side',
                'priority' => 'core',
            ], $this->view)->set()->with([
                'statuses' => $this->status,
            ]);
        }
    }

    /**
     * Return a defined post type property.
     *
     * @param null $property
     *
     * @return array
     *
     * @throws PostTypeException
     */
    public function get($property = null)
    {
        $args = [
            'name' => $this->datas['name'],
            'status' => $this->status,
        ];

        $properties = array_merge($args, $this->datas['args']);

        // If no property asked, return all defined properties.
        if (is_null($property) || empty($property)) {
            return $properties;
        }

        // If property exists, return it.
        if (isset($properties[$property])) {
            return $properties[$property];
        }

        throw new PostTypeException("Property '{$property}' does not exist on the '{$properties['name']}' custom post type.");
    }

    /**
     * Allow a user to change the title placeholder text.
     *
     * @param string $title The title placeholder text.
     *
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function setTitle($title)
    {
        $name = $this->datas['name'];

        $this->filter->add('enter_title_here', function ($default) use ($name, $title) {
            $screen = get_current_screen();

            if ($name == $screen->post_type) {
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
     * @param array        $args   The status arguments.
     *
     * @return \Themosis\PostType\PostTypeBuilder
     */
    public function status($status, array $args = [])
    {
        // Allow multiple statuses...
        if (is_array($status)) {
            foreach ($status as $key => $params) {
                if (is_int($key)) {
                    $this->status($params);
                } elseif (is_string($key) && is_array($params)) {
                    $this->status($key, $params);
                }
            }

            return $this;
        }

        // Set default arguments
        $defaultName = ucfirst($status);
        $args = wp_parse_args($args, [
            'label' => $defaultName,
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop($defaultName.' <span class="count">(%s)</span>', $defaultName.' <span class="count">(%s)</span>'),
            'publish_text' => __('Apply Changes'),
        ]);

        // Register the status
        $this->status[$status] = $args;

        // Remove default publish box
        $this->action->add('add_meta_boxes', [$this, 'removeDefaultPublishBox']);

        // Apply selected status on save.
        $this->filter->add('pre_post_status', [$this, 'applyStatus']);
        $this->filter->add('status_save_pre', [$this, 'applyStatus']);

        // Expose statuses to main JS object in wp-admin.
        $this->exposeStatuses();

        // Re-order the list of statuses on List Table.
        // Put the "trash" item as the last item.
        $this->filter->add('views_edit-'.$this->datas['name'], [$this, 'reorderStatusViews']);

        return $this;
    }

    /**
     * Re-order the status views/links on top of the List Table.
     * If there is a 'trash' view, move it to last position for better user experience.
     *
     * @param array $views The statuses views.
     *
     * @return array
     */
    public function reorderStatusViews($views)
    {
        if (array_key_exists('trash', $views)) {
            // Move it at the end of the views array.
            $trash = $views['trash'];
            unset($views['trash']);
            end($views); // move array pointer to the end.
            $views['trash'] = $trash; // add the trash view back.
        }

        return $views;
    }

    /**
     * Remove default publish metabox from the custom post type edit screen.
     */
    public function removeDefaultPublishBox()
    {
        // Remove current publish box
        remove_meta_box('submitdiv', $this->datas['name'], 'side');
    }

    /**
     * Handle output of custom statuses to admin JS object.
     */
    protected function exposeStatuses()
    {
        $self = $this;

        $this->filter->add('themosisAdminGlobalObject', function ($data) use ($self) {
            $cpt = new \stdClass();

            // Add the defined statuses.
            $cpt->statuses = $self->status;

            $data['_themosisPostTypes'][$self->get('name')] = $cpt;

            return $data;
        });
    }

    /**
     * Apply the selected status to the post on save.
     *
     * @param string $value The translated value by WordPress (is always "publish" for some reasons.)
     *
     * @return mixed
     */
    public function applyStatus($value)
    {
        // Check post_type and look if there are any custom statuses defined.
        if (isset($_POST['post_type']) && $this->datas['name'] === $_POST['post_type'] && !empty($this->status)) {
            if ((isset($_POST['post_status']) && 'publish' === $_POST['post_status']) && (isset($_REQUEST['post_status']) && 'draft' === $_REQUEST['post_status'])) {
                // New post with draft as default and "publish" button is clicked. Set to 1st registered post status.
                $statuses = array_keys($this->status);

                return esc_attr($statuses[0]);
            } elseif (isset($_REQUEST['post_status']) && !empty($_REQUEST['post_status'])) {
                /*
                 * In case of a quickedit ajax save call, check the value of the _status select tag
                 * before processing default post_status.
                 */
                if (isset($_POST['_status']) && !empty($_POST['_status'])) {
                    return esc_attr($_POST['_status']);
                }

                // Else simply apply the selected custom status.
                // Value return from the edit screen of the custom post type.
                return esc_attr($_REQUEST['post_status']);
            }
        }

        return esc_attr($value);
    }

    /**
     * Set the custom post type default arguments.
     *
     * @param string $plural   The post type plural display name.
     * @param string $singular The post type singular display name.
     *
     * @return array
     */
    protected function setDefaultArguments($plural, $singular)
    {
        $labels = [
            'name' => $plural,
            'singular_name' => $singular,
            'menu_name' => $plural
        ];

        $defaults = [
            'label' => $plural,
            'labels' => $labels,
            'description' => '',
            'public' => true,
            'menu_position' => 20,
            'has_archive' => true
        ];

        return $defaults;
    }

    /**
     * Check if the custom post type has statuses registered.
     *
     * @return bool
     */
    public function has_status()
    {
        return count($this->status) > 0;
    }

    /**
     * Return the WordPress post type instance.
     *
     * @return \stdClass|\WP_Post_type if WordPress 4.6+
     */
    public function instance()
    {
        return $this->postType;
    }
}
