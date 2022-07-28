<?php

namespace Themosis\Core;

class WordPressLoader
{
    /**
     * Load WordPress core files in an environment ready only.
     * For example: load WordPress from a custom command, ...
     */
    public function load(): void
    {
        $table_prefix = config(
            'database.connections.mysql.prefix',
            'wp_',
        );

        require web_path('cms/wp-settings.php');
    }
}
