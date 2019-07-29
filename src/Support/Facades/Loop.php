<?php

namespace Themosis\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static int id()
 * @method static string title($post = null)
 * @method static string author()
 * @method static string authorMeta(string $field = '', int $user_id = 0)
 * @method static string content($more_text = null, $strip_teaser = false)
 * @method static string excerpt($post = null)
 * @method static string thumbnail($size = 'post-thumbnail', $attr = '', $post = null)
 * @method static string|null thumbnailUrl($size = null, bool $icon = false)
 * @method static string link($post = 0, bool $leavename = false)
 * @method static array category(int $id = 0)
 * @method static array tags(int $id = 0)
 * @method static array|false|\WP_Error terms(string $taxonomy, $post = 0)
 * @method static string date(string $d = '', $post = null)
 * @method static string postClass($class = '', $post_id = null)
 * @method static string nextPage(?string $label = null, int $max_page = 0)
 * @method static string previousPage(?string $label = null)
 * @method static string|array paginate(array $args = [])
 *
 * @see \Themosis\View\Loop
 */
class Loop extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'loop';
    }
}
