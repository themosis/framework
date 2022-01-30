<?php

namespace Themosis\Core;

use Exception;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Mix
{
    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @param string $manifestDirectory
     *
     * @throws \Exception
     *
     * @return \Illuminate\Support\HtmlString|string
     */
    public function __invoke($path, $manifestDirectory = '')
    {
        static $manifests = [];

        // Default to the users theme if available, otherwise the public path
        if (! $manifestDirectory && function_exists('wp_get_theme')) {
            $manifestDirectory = '/content/themes/'.wp_get_theme()->stylesheet.'/dist';
        }

        if ($manifestDirectory == '/') {
            $manifestDirectory = '';
        }

        if (! Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }

        if ($manifestDirectory && ! Str::startsWith($manifestDirectory, '/')) {
            $manifestDirectory = "/{$manifestDirectory}";
        }

        if (file_exists(public_path($manifestDirectory.'/hot'))) {
            $url = rtrim(file_get_contents(public_path($manifestDirectory.'/hot')));

            if (Str::startsWith($url, ['http://', 'https://'])) {
                return new HtmlString(Str::after($url, ':').$path);
            }

            return new HtmlString("//localhost:8080{$path}");
        }

        $manifestPath = public_path($manifestDirectory.'/mix-manifest.json');

        if (! isset($manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (! isset($manifest[$path])) {
            $exception = new Exception("Unable to locate Mix file: {$path}.");

            if (! app('config')->get('app.debug')) {
                report($exception);

                return $path;
            } else {
                throw $exception;
            }
        }

        return new HtmlString(app('config')->get('app.mix_url').$manifestDirectory.$manifest[$path]);
    }
}
