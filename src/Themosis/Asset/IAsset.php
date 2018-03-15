<?php

namespace Themosis\Asset;

interface IAsset
{
    /**
     * Allow the developer to define where to load the asset.
     * Only 'admin', 'login' and 'customizer' are accepted. If none of those
     * values are used, simply keep the default front-end area.
     *
     * @param string $area Specify where to load the asset: 'admin', 'login' or 'customizer'.
     *
     * @return Asset
     */
    public function to($area);

    /**
     * Localize data for the linked asset.
     * Output JS object right before the script output.
     *
     * @param string $objectName The name of the JS variable that will hold the data.
     * @param mixed  $data       Any data to attach to the JS variable: string, boolean, object, array, ...
     *
     * @return Asset
     */
    public function localize($objectName, $data);

    /**
     * Remove a declared asset.
     *
     * @return Asset
     */
    public function remove();

    /**
     * Tells if an asset is queued or not.
     *
     * @return bool
     */
    public function isQueued();

    /**
     * Add attributes to the asset opening tag.
     *
     * @param array $atts The asset attributes to add.
     *
     * @return Asset
     */
    public function addAttributes(array $atts);
}
