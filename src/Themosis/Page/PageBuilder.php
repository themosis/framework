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
     * @var \Themosis\View\IRenderable
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
        Action::listen('admin_enqueue_scripts', $this, 'enqueueMediaUploader')->dispatch();
    }

    /**
     * @param string $slug The page slug name.
     * @param string $title The page display title.
     * @param string $parent The parent's page slug if a subpage.
     * @param IRenderable|string $view The page main view object or string in the form of controller|action.
     * @throws PageException
     * @return \Themosis\Page\PageBuilder
     */
    public function make($slug, $title, $parent = null, $view = null)
    {
        $params = compact('slug', 'title');

        foreach ($params as $name => $param)
        {
            if (!is_string($param))
            {
                throw new PageException('Invalid page parameter "'.$name.'"');
            }
        }

        // Check the view file.
        if (!is_null($view))
        {
            $this->view = $view;
        }

        // Set the page properties.
        $this->datas['slug'] = $slug;
        $this->datas['title'] = $title;
        $this->datas['parent'] = $parent;
        $this->datas['args'] = array(
            'capability'    => 'manage_options',
            'icon'          => '',
            'position'      => null,
            'tabs'          => true
        );
        $this->datas['rules'] = array();

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
        if (!is_null($this->datas['parent']))
        {
            add_submenu_page($this->datas['parent'], $this->datas['title'], $this->datas['title'], $this->datas['args']['capability'], $this->datas['slug'], array($this, 'displayPage'));
        }
        else
        {
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
        if(is_string($this->view))
        {   $parts = explode('@',$this->view);
            $controller = new $parts[0];
            $controller->__page = $this;
            $view = $controller->$parts[1]();
            echo $view->render();
        }
        else
        {   // Share the page instance to the view.
            $this->with('__page', $this);

            echo($this->view->render());
        }
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
     * Add settings to the page. Define settings per section
     * by setting the 'key' name equal to a registered section and
     * pass it an array of 'settings' fields.
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
        // The WordPress Settings API make
        // always use of sections and fields.
        // So let's set it up!
        if ($this->datas['args']['tabs'])
        {
            // A - With tabs
            $this->installWithTabs();
        }
        else
        {
            // B - Without tabs
            $this->installWithoutTabs();
        }
    }

    /**
     * Register sections and settings in order
     * to work with tabs.
     *
     * @return void
     */
    private function installWithTabs()
    {
        // 1 - Prepare the DB table.
        foreach ($this->sections as $section)
        {
            $section = $section->getData();

            if (false === get_option($section['slug']))
            {
                add_option($section['slug']);
            }
        }

        // 2 - Display sections
        foreach ($this->sections as $section)
        {
            $section = $section->getData();

            add_settings_section($section['slug'], $section['name'], array($this, 'displaySections'), $section['slug']);
        }

        // 3 - Display settings
        foreach ($this->settings as $section => $settings)
        {
            foreach ($settings as $setting)
            {
                // Add the section to the field.
                $setting['section'] = $section;

                add_settings_field($setting['name'], $setting['title'], array($this, 'displaySettings'), $section, $section, $setting);
            }
        }

        // 4 - Register the settings and define the sanitized callback
        // Group all page settings in one, avoid polluting
        // the wp_options table.
        // When you want to retrieve a setting use the option_group
        // name and the setting id.
        foreach ($this->sections as $section)
        {
            $section = $section->getData();

            register_setting($section['slug'], $section['slug'], array($this, 'validateSettings'));
        }
    }

    /**
     * Register sections and settings in a page.
     *
     * @return void
     */
    private function installWithoutTabs()
    {
        // 1 - Prepare the DB table.
        if (false === get_option($this->datas['slug']))
        {
            add_option($this->datas['slug']);
        }

        // 2 - Display sections
        foreach ($this->sections as $section)
        {
            $section = $section->getData();

            add_settings_section($section['slug'], $section['name'], array($this, 'displaySections'), $this->datas['slug']);
        }

        // 3 - Display settings
        foreach ($this->settings as $section => $settings)
        {
            foreach ($settings as $setting)
            {
                // Add the section to the field - In this case,
                // it is associated to the page slug.
                $setting['section'] = $this->datas['slug'];

                add_settings_field($setting['name'], $setting['title'], array($this, 'displaySettings'), $this->datas['slug'], $section, $setting);
            }
        }

        // 4 - Register the settings and define the sanitized callback
        // Group all page settings in one, avoid polluting
        // the wp_options table.
        // When you want to retrieve a setting use the option_group
        // name and the setting id.
        register_setting($this->datas['slug'], $this->datas['slug'], array($this, 'validateSettings'));
    }

    /**
     * Handle section display of the Settings API.
     *
     * @param array $args
     * @return void
     */
    public function displaySections(array $args)
    {
    }

    /**
     * Handle setting display of the Settings API.
     *
     * @param mixed $setting
     * @return void
     */
    public function displaySettings($setting)
    {
        // Check if a registered value exists.
        $value = get_option($setting['section']);
        $val = isset($value[$setting['name']]) ? $value[$setting['name']] : null;
        $setting['value'] = (!is_null($val) || !empty($val)) ? $val : $this->parseValue($setting, $val);

        // Set the name attribute.
        $setting['name'] = $setting['section'].'['.$setting['name'].']';

        // Display the setting.
        echo($setting->page());
    }

    /**
     * Validate the defined settings.
     *
     * @param mixed $values
     * @return array
     */
    public function validateSettings($values)
    {
        // No validation rules
        if (!isset($this->datas['rules']) || !is_array($this->datas['rules'])) return $values;

        // Null given
        if (is_null($values)) return array();

        $sanitized = array();

        foreach ($values as $setting => $value)
        {
            $rules = array_keys($this->datas['rules']);

            // 1 - Check if a rule exists
            if (in_array($setting, $rules))
            {
                // 1.1 - Check for infinite settings.
                if (is_array($value) && $this->isInfinite($setting))
                {
                    foreach ($value as $index => $row)
                    {
                        if ($this->validator->isAssociative($row) && !empty($row))
                        {
                            foreach ($row as $infiniteSetting => $infiniteValue)
                            {
                                // 1.1.1 - Check if a rule is defined for the infinite sub fields.
                                if (isset($this->datas['rules'][$setting][$infiniteSetting]))
                                {
                                    $rule = $this->datas['rules'][$setting][$infiniteSetting];

                                    $sanitized[$setting][$index][$infiniteSetting] = $this->validator->single($infiniteValue, $rule);
                                }
                                else
                                {
                                    $sanitized[$setting][$index][$infiniteSetting] = $infiniteValue;
                                }
                            }
                        }
                    }
                }
                else
                {
                    // 1.2 - Apply rule to other settings.
                    $sanitized[$setting] = $this->validator->single($value, $this->datas['rules'][$setting]);
                }

            }
            else
            {
                // 2 - No rule, just set the default value.
                $sanitized[$setting] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Set validation rules to settings.
     *
     * @param array $rules
     * @return \Themosis\Page\PageBuilder
     */
    public function validate(array $rules = array())
    {
        $this->datas['rules'] = $rules;

        return $this;
    }

    /**
     * Check if a field/settings is of type 'infinite'.
     *
     * @param string $name The name of the field/setting.
     * @return bool
     */
    private function isInfinite($name)
    {
        foreach ($this->settings as $settings)
        {
            foreach ($settings as $setting)
            {
                if($name === $setting['name']) return true;
            }
        }

        return false;
    }

    /**
     * Return the active tab slug of the settings page.
     *
     * @return string
     */
    private function getActiveTab()
    {
        $firstSection = $this->sections[0]->getData();
        return isset($_GET['tab']) ? $_GET['tab'] : $firstSection['slug'];
    }

    /**
     * Helper method that output the tab navigation
     * if available.
     *
     * @return void
     */
    public function renderTabs()
    {
        if ($this->hasSections() && $this->datas['args']['tabs'])
        {
            echo('<h2 class="nav-tab-wrapper">');

                foreach ($this->sections as $section)
                {
                    $section = $section->getData();
                    $class = ($this->getActiveTab() === $section['slug']) ? 'nav-tab-active' : '';

                    printf('<a href="?page=%s&tab=%s" class="nav-tab %s">%s</a>', $this->datas['slug'], $section['slug'], $class, $section['name']);
                }

            echo('</h2>');
        }
    }

    /**
     * Helper method that output the page settings.
     *
     * @return void
     */
    public function renderSettings()
    {
        // Check if there are sections before proceeding.
        if (!$this->hasSections()) return;

        if ($this->datas['args']['tabs'])
        {
            foreach ($this->sections as $section)
            {
                $section = $section->getData();

                // Display settings regarding the active tab.
                if ($this->getActiveTab() === $section['slug'])
                {
                    settings_fields($section['slug']);
                    do_settings_sections($section['slug']);
                }
            }
        }
        else
        {
            // Do not use the tab navigation.
            // Display all sections in one page.
            settings_fields($this->datas['slug']);
            do_settings_sections($this->datas['slug']);
        }
    }

    /**
     * Enqueue the WordPress media scripts.
     * Make the 'wp' object available to javascript.
     *
     * @return void
     */
    public function enqueueMediaUploader()
    {
        // If WordPress version > 3.5
        if (get_bloginfo('version') >= 3.5)
        {
            wp_enqueue_media();
        }
    }

} 