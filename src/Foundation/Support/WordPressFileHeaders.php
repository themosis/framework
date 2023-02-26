<?php

namespace Themosis\Foundation\Support;

trait WordPressFileHeaders
{
    /**
     * Return the headers of a WordPress file.
     */
    public function headers(string $path, array $headers): array
    {
        $data = $this->read($path);
        $properties = [];

        foreach ($headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $data, $match) && $match[1]) {
                $properties[$field] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
            } else {
                $properties[$field] = '';
            }
        }

        return $properties;
    }

    /**
     * Get a partial content of given file.
     */
    public function read(string $path, int $length = 8192): string
    {
        $handle = fopen($path, 'r');
        $content = fread($handle, $length);
        fclose($handle);

        return str_replace("\r", "\n", $content);
    }
}
