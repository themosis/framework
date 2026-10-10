Themosis Framework 12
=====================

A modernization of the [Themosis framework](https://github.com/themosis/framework) 3.1 (WordPress framework built on Laravel components) to **Laravel 12**, **PHP 8.4/8.5** and **WordPress 7**.

> [!WARNING]
> **This fork is not meant to be stable or maintained for third-party users.**
> It is a sane modernization of the work of the original author, kept up to date for a handful of existing projects.
> There is no support, no backward compatibility promise and it may change at any time.
> Issues and pull requests are not monitored.

Versioning
----------

The major version follows the Laravel major version: **12.x requires Laravel 12** (`illuminate/*` `^12.69`).

Requirements
------------

- PHP `^8.2` (used on PHP 8.4 and 8.5)
- WordPress 6.8+ (used on WordPress 7.1)
- Composer 2

Installation
------------

Add the repository and require the package in an existing Themosis application:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/fatk/themosis-framework" }
    ],
    "require": {
        "themosis/framework": "^12.0"
    }
}
```

The [original documentation](https://framework.themosis.com) still applies to the framework APIs (routes, post types, taxonomies, metaboxes, fields, forms, pages, hooks...).

Changes from Themosis 3.1
-------------------------

### Dependencies

| Package                    | Themosis 3.1 | Themosis 12          |
|----------------------------|--------------|----------------------|
| PHP                        | `^8.0`       | `^8.2`               |
| `illuminate/*`             | `^8.83`      | `^12.69`             |
| `league/flysystem`         | `^1.1`       | `^3.29`              |
| `laravel/ui`               | `^3.1`       | `^4.0`               |
| `laravel/tinker`           | `^2.5`       | `^2.10` / `^3.0`     |
| `composer/installers`      | `^1.9`       | `^2`                 |
| `predis/predis`            | `^1.1`       | `^2.3` / `^3.0`      |
| `symfony/property-access`  | `^5.4`       | `^7.4` / `^8.0`      |
| `paragonie/sodium_compat`  | `^1.15`      | `^1.24.2` / `^2.5.2` |
| `guzzlehttp/guzzle`        | `^7.2`       | `^7.8`               |
| `league/fractal`           | `^0.20`      | `^0.20` / `^0.21`    |
| PHPUnit (dev)              | `^9`         | `^12` / `^13`        |

### Major fixes

- **Laravel 9 → 12 port**: container, contracts and helpers aligned with Laravel 12 (`Application::register()` force flag, `publicPath()`, `useStoragePath()`, `maintenanceMode()`, fallback locale, terminating callbacks, removed `symfony/debug`, `SerializableClosure` and `DetectsApplicationNamespace` usages).
- **Errors & deprecations**: PHP warnings and notices are logged instead of rendering an error page; deprecations go to a dedicated log channel (on when `WP_DEBUG` is enabled, off otherwise) and no longer depend on the `error_reporting()` mask; PHP 8.4+ deprecations fixed.
- **Console**: `migrate*` and `schedule:*` commands restored, closure commands and `#[AsCommand]` commands fixed, `vendor:publish` works with Flysystem 3, `make:auth` fixed.
- **Queues**: queued closures, closure chains and scheduled closures work with standalone `illuminate/*` components.
- **Production caches**: `config:cache` keeps `.env` available to WordPress configuration and themes, `route:cache` no longer drops theme routes, `optimize` / `optimize:clear` cover config, events, routes and views.
- **Service providers**: Metabox, Page and Composer providers are deferred again (`DeferrableProvider`).
- **Laravel parity**: exception handler `ignore()` / `stopIgnoring()` / `level()`, `FormRequest::validated($key, $default)`, automatic policy registration, `current_password` never flashed.

### Additions

- `opcache:clear`: resets the **PHP-FPM** OPcache from the CLI through FastCGI (`--fcgi=host:port|/path/to.sock`, or `OPCACHE_FCGI`, default `127.0.0.1:9000`).

Production
----------

After a deployment:

```bash
php artisan optimize        # config, events, routes and views
php artisan opcache:clear   # reset the PHP-FPM OPcache last
```

Theme routes and theme views are registered when WordPress loads the theme: they are not part of the route and view caches and keep loading at runtime.

Deprecations logging (`config/logging.php`):

```php
'deprecations' => [
    'channel' => env('LOG_DEPRECATIONS_CHANNEL'), // null: follows WP_DEBUG, false: disabled
    'trace' => env('LOG_DEPRECATIONS_TRACE', false),
],
```

Credits
-------

- **[Julien Lambé](https://github.com/jlambe)**, author of the Themosis framework, and the [Themosis contributors](https://github.com/themosis/framework/graphs/contributors).
- Some fixes are ported from other community forks: [digitalmeat](https://github.com/digitalmeat/themosis-framework), [MyronS-Leobit](https://github.com/MyronS-Leobit/themosis-framework), [AmphiBee](https://github.com/AmphiBee/framework), [brand42](https://github.com/brand42/themosis-framework).

License
-------

GPL-2.0-or-later, see [LICENSE.md](LICENSE.md).
