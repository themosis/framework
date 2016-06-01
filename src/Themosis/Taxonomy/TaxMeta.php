<?php

namespace Themosis\Taxonomy;

/**
 * TaxMeta class.
 * 
 * Allow the user to retrieve a custom field of a taxonomy.
 */
class TaxMeta
{
    /**
     * Retrieve all custom fields of a term.
     *
     * @param string $taxonomySlug The registered taxonomy slug.
     * @param int    $term_id      The term ID.
     *
     * @return array|bool The custom field values. False if empty.
     */
    public static function all($taxonomySlug, $term_id)
    {
        $key = $taxonomySlug.'_'.$term_id;

        return get_option($key);
    }

    /**
     * Retrieve one custom field of a term.
     *
     * @param string $taxonomySlug The registered taxonomy slug.
     * @param int    $term_id      The term ID.
     * @param string $key          The key name of the custom field.
     *
     * @return array|string The saved value in the option table.
     */
    public static function get($taxonomySlug, $term_id, $key)
    {
        $values = static::all($taxonomySlug, $term_id);

        return $values[$key];
    }
}
