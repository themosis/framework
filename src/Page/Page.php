<?php

namespace Themosis\Page;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Hook\IHook;
use Themosis\Page\Contracts\PageInterface;
use Themosis\Page\Contracts\SettingsRepositoryInterface;
use Themosis\Support\CallbackHandler;
use Themosis\Support\Contracts\SectionInterface;
use Themosis\Support\Contracts\UIContainerInterface;

class Page implements PageInterface
{
    use CallbackHandler;

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
     * @var IHook
     */
    protected $filter;

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

    /**
     * @var Factory
     */
    protected $validator;

    /**
     * @var int
     */
    protected $errors = 0;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * List of pages titles per route action.
     * Only used if multiple routes are defined
     * for the current page. Default to main
     * $title property.
     *
     * @var array
     */
    protected $titles = [];

    /**
     * @var bool
     */
    protected $showInRest = false;

    public function __construct(
        IHook $action,
        IHook $filter,
        UIContainerInterface $ui,
        SettingsRepositoryInterface $repository,
        Factory $validator
    ) {
        $this->action = $action;
        $this->filter = $filter;
        $this->ui = $ui;
        $this->repository = $repository;
        $this->validator = $validator;
        $this->setContainer($this->getContainer());
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
     * Set page settings global show in rest property.
     *
     * @param bool $show
     *
     * @return PageInterface
     */
    public function showInRest($show = true): PageInterface
    {
        $this->showInRest = $show;

        return $this;
    }

    /**
     * Return the global page property show in rest.
     *
     * @return bool
     */
    public function isShownInRest(): bool
    {
        return $this->showInRest;
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
     * Find the page parent hook if any.
     *
     * @return string
     */
    protected function findParentHook(): string
    {
        if (! $this->hasParent()) {
            return '';
        }

        // Check if parent attribute is attached to a custom post type or another page.
        if (false !== $pos = strpos($this->getParent(), 'post_type=')) {
            // Parent hook is equivalent to the post type slug value.
            return substr($this->getParent(), $pos + 10);
        } else {
            switch (trim($this->getParent(), '\/?&')) {
                case 'index.php':
                    return 'dashboard';
                case 'edit.php':
                    return 'posts';
                case 'upload.php':
                    return 'media';
                case 'edit-comments.php':
                    return 'comments';
                case 'themes.php':
                    return 'appearance';
                case 'plugins.php':
                    return 'plugins';
                case 'users.php':
                    return 'users';
                case 'tools.php':
                    return 'tools';
                case 'options-general.php':
                    return 'settings';
            }
        }

        // The current page is attached to another one.
        $abstract = 'page.'.$this->getParent();

        if ($this->ui()->factory()->getContainer()->bound($abstract)) {
            // Parent hook is equivalent to the page menu as lowercase.
            $parent = $this->ui()->factory()->getContainer()->make($abstract);

            return Str::kebab(sanitize_title(strtolower($parent->getMenu())));
        }

        return '';
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
        if ('page' === $this->ui()->getViewPath()) {
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
                $setting = $this->prepareSetting($setting);

                // Display the setting.
                add_settings_field(
                    $setting->getName(),
                    $setting->getOption('label'),
                    [$this, 'renderSettings'],
                    $this->getSlug(),
                    $slug,
                    $setting
                );

                // Validate setting.
                $showInRest = $this->isShownInRest();

                if ($setting->getOption('show_in_rest', false)) {
                    $showInRest = true;
                }

                register_setting($this->getSlug(), $setting->getName(), [
                    'sanitize_callback' => [$this, 'sanitizeSetting'],
                    'default' => $setting->getOption('data', ''),
                    'show_in_rest' => $showInRest,
                    'type' => $setting->getOption('data_type', 'string')
                ]);
            }
        }
    }

    /**
     * Prepare the setting.
     *
     * @param FieldTypeInterface $setting
     *
     * @return FieldTypeInterface
     */
    protected function prepareSetting(FieldTypeInterface $setting)
    {
        if (empty($setting->getTheme()) || is_array($setting->getTheme())) {
            // Page settings only have the "themosis" theme available.
            $setting->setTheme('themosis.pages');
        }

        $setting->setPrefix($this->getPrefix());
        $setting->setOptions([
            'label' => $setting->getOption('label', ucfirst($setting->getBaseName())),
            'placeholder' => $setting->getOption('placeholder', $setting->getBaseName())
        ]);

        $attributes = array_merge([
            'class' => 'regular-text'
        ], $setting->getAttributes());
        $setting->setAttributes($attributes);

        return $setting;
    }

    /**
     * Sanitize the setting before save.
     *
     * @param string|array $value
     *
     * @return string|array
     */
    public function sanitizeSetting($value)
    {
        $keys = $this->repository()->getSettings()->collapse()->map(function ($setting) {
            /** @var FieldTypeInterface $setting */
            return $setting->getName();
        });

        $settingName = $keys->slice($this->offset, 1)->first();
        $lastSetting = $this->repository()->getSettings()->collapse()->last();
        $data = collect($_POST);

        if ($this->offset > $keys->count() - 1) {
            if (empty($value)) {
                return '';
            }

            // Sanitize is called one more time with a valid value.
            // Let's get the $settingName based on the given value
            // as we can't rely anymore on the offset.
            $settingName = $data->search($value, true);

            if (! $settingName) {
                return '';
            }

            // Let's add a "fake" error to avoid duplicate success messages.
            $this->errors++;
        }

        $setting = $this->repository()->getSettingByName($settingName);

        $validator = $this->validator->make(
            $data->all(),
            [$setting->getName() => $setting->getOption('rules')],
            $this->getSettingMessages($setting),
            $this->getSettingPlaceholder($setting)
        );

        // Update setting offset.
        $this->offset++;

        /** @var Validator $validator */
        if ($validator->fails()) {
            $this->errors++;

            $this->addSettingsErrorMessage(
                $this->getSlug(),
                $setting->getName(),
                $validator->getMessageBag()->first($setting->getName())
            );

            return '';
        }

        if ($settingName === $lastSetting->getName() && ! $this->errors) {
            $this->addSettingsSuccessMessage($this->getSlug());
        }

        return $value;
    }

    /**
     * Add settings error message.
     *
     * @param string $slug
     * @param string $name
     * @param string $message
     */
    private function addSettingsErrorMessage(string $slug, string $name, string $message)
    {
        add_settings_error($slug, $name, $message, 'error');
    }

    /**
     * Add settings success message.
     *
     * @param string $slug
     */
    private function addSettingsSuccessMessage(string $slug)
    {
        add_settings_error(
            $slug,
            'settings_updated',
            __('Settings saved.'),
            'updated'
        );
    }

    /**
     * Return the setting custom error messages.
     *
     * @param FieldTypeInterface $setting
     *
     * @return array
     */
    protected function getSettingMessages(FieldTypeInterface $setting): array
    {
        $messages = [];

        foreach ($setting->getOption('messages', []) as $attr => $message) {
            $messages[$setting->getName().'.'.$attr] = $message;
        }

        return $messages;
    }

    /**
     * Return the setting placeholder.
     *
     * @param FieldTypeInterface $setting
     *
     * @return array
     */
    protected function getSettingPlaceholder(FieldTypeInterface $setting): array
    {
        $placeholder = $setting->getOption('placeholder');

        if (is_null($placeholder)) {
            return [];
        }

        return [$setting->getName() => $placeholder];
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
        // Set the setting value if any.
        $value = get_option($setting->getName(), null);

        if (! is_null($value)) {
            $setting->setValue($value);
        }

        $view = sprintf('%s.%s', $this->ui()->getTheme(), $setting->getView(false));

        echo $this->ui()->factory()->make($view)->with([
            '__field' => $setting,
            '__page' => $this
        ])->render();
    }

    /**
     * Return the setting error from its name.
     *
     * @param string $name
     *
     * @return array
     */
    public function getSettingError(string $name): array
    {
        $errors = get_settings_errors($this->getSlug());

        if (empty($errors)) {
            return [];
        }

        return collect($errors)->first(function ($error) use ($name) {
            return $error['code'] === $name;
        });
    }

    /**
     * Set the page view path.
     *
     * @param string $name
     * @param bool   $useShortPath
     *
     * @return PageInterface
     */
    public function setView(string $name, bool $useShortPath = false): PageInterface
    {
        $this->ui()->useShortPath($useShortPath)->setView($name);

        return $this;
    }

    /**
     * Check if current page has a parent.
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return ! is_null($this->parent);
    }

    /**
     * Parse page GET requests.
     */
    public function parseGetRoute()
    {
        $request = $this->getRequest();

        if (is_null($request) || ! isset($this->routes['get'])) {
            return;
        }

        $action = $request->get('action', '/');

        if (in_array($action, array_keys($this->routes['get']))) {
            $callback = $this->routes['get'][$action];
            $response = $this->handleCallback($callback);

            if (! is_a($response, Renderable::class)) {
                throw new \Exception('The controller method must return a view instance.');
            }

            // Set the page view.
            $this->ui()->setViewInstance($response);
        }
    }

    /**
     * Parse page POST requests.
     *
     * Note: POST requests should always target "admin-post.php"
     * on a custom page form.
     */
    public function parsePostRoute()
    {
        if (empty($this->routes) || ! isset($this->routes['post'])) {
            return;
        }

        foreach ($this->routes['post'] as $action => $callback) {
            $this->action->add('admin_post_'.$action, $callback);
        }
    }

    /**
     * Register page routes.
     *
     * @param string          $action
     * @param callable|string $callback
     * @param string          $method
     * @param string          $title
     *
     * @return PageInterface
     */
    public function route(string $action, $callback, string $method = 'get', string $title = ''): PageInterface
    {
        $method = strtolower($method);
        $action = $this->parseAction($action, $method);

        $this->routes[$method][$action] = $callback;

        $this->titles[$action] = ! empty($title) ? $title : $this->getTitle();

        $this->registerRouteActions();

        return $this;
    }

    /**
     * Register page routes actions and filters.
     */
    protected function registerRouteActions()
    {
        // Actions for page routing.
        $this->action->add('load-toplevel_page_'.$this->getSlug(), [$this, 'parseGetRoute']);
        $this->action->add('load-admin_page_'.$this->getSlug(), [$this, 'parseGetRoute']);
        $this->action->add('load-'.$this->findParentHook().'_page_'.$this->getSlug(), [$this, 'parseGetRoute']);
        $this->action->add('admin_init', [$this, 'parsePostRoute']);

        $this->filter->add('admin_title', [$this, 'handleTitle']);
    }

    /**
     * Format the action name.
     *
     * @param string $action
     * @param string $method
     *
     * @return string
     */
    protected function parseAction(string $action, string $method): string
    {
        if ('post' === $method) {
            return $this->getSlug().'_'.$action;
        }

        return $action;
    }

    /**
     * Called by the "admin_title" filter. Handle the page titles.
     *
     * @param string $title
     *
     * @return string
     */
    public function handleTitle($title)
    {
        if (is_null($request = $this->getRequest()) || empty($this->titles)) {
            return $title;
        }

        if (in_array($action = $request->get('action'), array_keys($this->titles))) {
            return $this->titles[$action];
        }

        return $title;
    }

    /**
     * Return the service container instance.
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        if (is_null($this->container)) {
            $this->container = $this->ui()->factory()->getContainer();
        }

        return $this->container;
    }

    /**
     * Get the application request instance.
     *
     * @return null|Request
     */
    protected function getRequest()
    {
        $container = $this->getContainer();

        if ($container->bound('request')) {
            return $container['request'];
        }

        return null;
    }

    /**
     * Return the action name for POST requests.
     *
     * @param string $action
     *
     * @return string
     */
    public function getAction(string $action): string
    {
        return $this->parseAction($action, 'post');
    }
}
