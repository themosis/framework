<?php

namespace Themosis\PostType;

interface IPostType
{
    /**
     * Method called to build a post type instance.
     *
     * @param string $name     The custom post type name.
     * @param string $plural   The custom post type plural display name.
     * @param string $singular The custom post type singular display name.
     *
     * @return \Themosis\PostType\IPostType
     */
    public function make($name, $plural, $singular);

    /**
     * Method called to register the post type into WordPress.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function set(array $params = array());

    /**
     * Method to return defined post type properties.
     *
     * @param null $property
     *
     * @return mixed
     */
    public function get($property = null);

    /**
     * Allow a user to change the title placeholder text.
     *
     * @param string $title
     *
     * @return \Themosis\PostType\IPostType
     */
    public function setTitle($title);

    /**
     * Allow user to register custom post type statuses.
     *
     * @param array|string $status The status key name.
     * @param array        $args   The status arguments.
     *
     * @return \Themosis\PostType\IPostType
     */
    public function status($status, array $args = []);
}
