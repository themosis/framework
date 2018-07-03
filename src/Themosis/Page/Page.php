<?php

namespace Themosis\Page;

use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Hook\IHook;
use Themosis\Page\Contracts\PageInterface;
use Themosis\Page\Contracts\SettingsRepositoryInterface;
use Themosis\Support\Contracts\SectionInterface;
use Themosis\Support\Contracts\UIContainerInterface;

class Page implements PageInterface
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $menu;

    /**
     * @var string
     */
    protected $cap = 'manage_options';

    /**
     * @var string
     */
    protected $icon = 'dashicons-admin-generic';

    /**
     * @var int
     */
    protected $position = 21;

    /**
     * @var string
     */
    protected $parent;

    /**
     * @var bool
     */
    protected $network = false;

    /**
     * @var IHook
     */
    protected $action;

    /**
     * @var UIContainerInterface
     */
    protected $ui;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $prefix = 'th_';

    public function __construct(IHook $action, UIContainerInterface $ui, SettingsRepositoryInterface $repository)
    {
        $this->action = $action;
        $this->ui = $ui;
        $this->repository = $repository;
    }

    /**
     * Return the page slug.
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Set the page slug.
     *
     * @param string $slug
     *
     * @return PageInterface
     */
    public function setSlug(string $slug): PageInterface
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Return the page title.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the page title.
     *
     * @param string $title
     *
     * @return PageInterface
     */
    public function setTitle(string $title): PageInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Return the page menu.
     *
     * @return string
     */
    public function getMenu(): string
    {
        return $this->menu;
    }

    /**
     * Set the page menu.
     *
     * @param string $menu
     *
     * @return PageInterface
     */
    public function setMenu(string $menu): PageInterface
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Return the page capability.
     *
     * @return string
     */
    public function getCapability(): string
    {
        return $this->cap;
    }

    /**
     * Set the page capability.
     *
     * @param string $cap
     *
     * @return PageInterface
     */
    public function setCapability(string $cap): PageInterface
    {
        $this->cap = $cap;

        return $this;
    }

    /**
     * Return the page icon.
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Set the page icon.
     *
     * @param string $icon
     *
     * @return PageInterface
     */
    public function setIcon(string $icon): PageInterface
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Return the page position.
     *
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set the page position.
     *
     * @param int $position
     *
     * @return PageInterface
     */
    public function setPosition(int $position): PageInterface
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Return the page parent.
     *
     * @return null|string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the page parent.
     *
     * @param string $parent
     *
     * @return PageInterface
     */
    public function setParent(string $parent): PageInterface
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Set the page for network display.
     *
     * @param bool $network
     *
     * @return PageInterface
     */
    public function network(bool $network = true): PageInterface
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Check if the page is for network display.
     *
     * @return bool
     */
    public function isNetwork(): bool
    {
        return $this->network;
    }

    /**
     * Set the page. Display it on the WordPress administration.
     *
     * @return PageInterface
     */
    public function set(): PageInterface
    {
        $hook = $this->isNetwork() ? 'network_admin_menu' : 'admin_menu';

        // Action for page display.
        $this->action->add($hook, [$this, 'build']);
        // Action for page settings.
        $this->action->add('admin_init', [$this, 'configureSettings']);

        return $this;
    }

    /**
     * Build the WordPress pages.
     */
    public function build()
    {
        if (is_null($this->getParent())) {
            // Add a top menu page.
            add_menu_page(
                $this->getTitle(),
                $this->getMenu(),
                $this->getCapability(),
                $this->getSlug(),
                [$this, 'render'],
                $this->getIcon(),
                $this->getPosition()
            );
        } else {
            // Add a submenu page.
            add_submenu_page(
                $this->getParent(),
                $this->getTitle(),
                $this->getMenu(),
                $this->getCapability(),
                $this->getSlug(),
                [$this, 'render']
            );
        }
    }

    /**
     * Render/output the page HTML.
     */
    public function render()
    {
        echo $this->ui()->getView()->with([
            '__page' => $this
        ])->render();
    }

    /**
     * Return the page view layer.
     *
     * @return UIContainerInterface
     */
    public function ui(): UIContainerInterface
    {
        return $this->ui;
    }

    /**
     * Add data to the page view.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return PageInterface
     */
    public function with($key, $value = null): PageInterface
    {
        $this->ui()->getView()->with($key, $value);

        return $this;
    }

    /**
     * Return the page settings repository.
     *
     * @return SettingsRepositoryInterface
     */
    public function repository(): SettingsRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * Add sections to the page.
     *
     * @param array $sections
     *
     * @return PageInterface
     */
    public function addSections(array $sections): PageInterface
    {
        $sections = array_merge(
            $this->repository()->getSections()->all(),
            $sections
        );

        array_walk($sections, function ($section) {
            // Set a default view to each section if none defined.
            /** @var SectionInterface $section */
            if (empty($section->getView())) {
                $section->setView('section');
            }
        });

        $this->repository()->setSections($sections);

        return $this;
    }

    /**
     * Add settings to the page.
     *
     * @param string|array $section
     * @param array        $settings
     *
     * @return PageInterface
     */
    public function addSettings($section, array $settings = []): PageInterface
    {
        $currentSettings = $this->repository()->getSettings()->all();

        if (is_array($section)) {
            $settings = array_merge($currentSettings, $section);
        } else {
            $settings = array_merge($currentSettings, [$section => $settings]);
        }

        $this->repository()->setSettings($settings);

        // Set a default page view for handling
        // the settings. A user can still overwrite
        // the view.
        if ('options' !== $this->ui()->getViewPath()) {
            $this->ui()->setView('options');
        }

        return $this;
    }

    /**
     * Return the page prefix.
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Set the page settings name prefix.
     *
     * @param string $prefix
     *
     * @return PageInterface
     */
    public function setPrefix(string $prefix): PageInterface
    {
        $this->prefix = $prefix;

        $this->repository()->getSettings()->collapse()->each(function ($setting) use ($prefix) {
            /** @var $setting FieldTypeInterface */
            $setting->setPrefix($prefix);
        });

        return $this;
    }

    /**
     * Configure page settings if any.
     * Called by the "admin_init" hook.
     */
    public function configureSettings()
    {
        // If no settings && sections, return.
        $settings = $this->repository()->getSettings();
        $sections = $this->repository()->getSections();

        if ($settings->isEmpty() && $sections->isEmpty()) {
            return;
        }

        // Configure sections.
        $sections->each(function ($section) {
            /** @var SectionInterface $section */
            add_settings_section($section->getId(), $section->getTitle(), [$this, 'renderSections'], $this->getSlug());
        });

        // Configure settings.
        foreach ($settings->all() as $slug => $fields) {
            foreach ($fields as $setting) {
                /** @var FieldTypeInterface $setting */
                // Set prefix.
                $setting->setPrefix($this->getPrefix());
                // Set a default setting title based on its basename.
                $setting->setOptions([
                    'label' => ucfirst($setting->getBaseName()),
                    'attributes' => [
                        'id' => $setting->getName().'_setting'
                    ]
                ]);
                // Set a default CSS class.
                $setting->addAttribute('class', 'regular-text');

                // Display the setting.
                add_settings_field(
                    $setting->getName(),
                    $setting->getOptions('label'),
                    [$this, 'renderSettings'],
                    $this->getSlug(),
                    $slug,
                    $setting
                );
            }
        }

        // Validate settings.
        // @todo Validate settings
    }

    /**
     * Output the section HTML.
     *
     * @param array $args
     */
    public function renderSections(array $args)
    {
        $section = $this->repository()->getSectionByName($args['id']);
        $view = sprintf(
            '%s.%s.%s',
            $this->ui()->getTheme(),
            $this->ui()->getLayout(),
            $section->getView()
        );

        echo $this->ui()->factory()->make($view)->with($section->getViewData())->render();
    }

    /**
     * Output the setting HTML.
     *
     * @param FieldTypeInterface $setting
     */
    public function renderSettings($setting)
    {
        $view = sprintf('%s.%s', $this->ui()->getTheme(), $setting->getView(false));

        echo $this->ui()->factory()->make($view)->with([
            '__field' => $setting
        ])->render();
    }
}
