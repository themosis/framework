<?php

namespace Themosis\Metabox\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Metabox\MetaboxInterface;

class MetaboxTransformer extends TransformerAbstract
{
    /**
     * Transform the metabox to a resource.
     *
     * @param MetaboxInterface $metabox
     *
     * @return array
     */
    public function transform(MetaboxInterface $metabox)
    {
        return [
            'id' => $metabox->getId(),
            'title' => $metabox->getTitle(),
            'screen' => $this->getScreen($metabox->getScreen()),
            'context' => $metabox->getContext(),
            'priority' => $metabox->getPriority()
        ];
    }

    /**
     * Get the metabox screen data.
     *
     * @param $screen
     *
     * @return array
     */
    protected function getScreen($screen)
    {
        if (is_string($screen) && function_exists('convert_to_screen')) {
            $screen = convert_to_screen($screen);
        }

        if ($screen instanceof \WP_Screen) {
            return [
                'id' => $screen->id,
                'post_type' => $screen->post_type
            ];
        }

        // Screen is still a string.
        return [
            'id' => $screen,
            'post_type' => $screen
        ];
    }
}
