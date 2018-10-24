<?php

namespace Themosis\Core;

use Illuminate\Support\Collection;

class HelloDolly
{
    /**
     * Return a lyric.
     *
     * Tribute to WordPress.
     * Based on the Hello Dolly plugin built by Matt Mullenweg.
     *
     * @see https://wordpress.org/plugins/hello-dolly/
     *
     * @return string
     */
    public static function lyric()
    {
        return Collection::make([
            "Hello, Dolly",
            "Well, hello, Dolly",
            "It's so nice to have you back where you belong",
            "You're lookin' swell, Dolly",
            "I can tell, Dolly",
            "You're still glowin', you're still crowin'",
            "You're still goin' strong",
            "I feel the room swayin'",
            "While the band's playin'",
            "One of our old favorite songs from way back when",
            "So, take her wrap, fellas",
            "Dolly, never go away again",
            "Hello, Dolly",
            "Well, hello, Dolly",
            "It's so nice to have you back where you belong",
            "You're lookin' swell, Dolly",
            "I can tell, Dolly",
            "You're still glowin', you're still crowin'",
            "You're still goin' strong",
            "I feel the room swayin'",
            "While the band's playin'",
            "One of our old favorite songs from way back when",
            "So, golly, gee, fellas",
            "Have a little faith in me, fellas",
            "Dolly, never go away",
            "Promise, you'll never go away",
            "Dolly'll never go away again"
        ])->random();
    }
}
