<?php

namespace Themosis\Metabox\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Forms\Contracts\FieldTypeInterface;
use Themosis\Forms\Resources\Factory;
use Themosis\Forms\Resources\Transformers\FieldTransformer;
use Themosis\Forms\Resources\Transformers\GroupTransformer;
use Themosis\Metabox\Contracts\MetaboxInterface;

class MetaboxTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = [
        'fields',
        'groups'
    ];

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
            'context' => $metabox->getContext(),
            'l10n' => $metabox->getTranslations(),
            'locale' => $metabox->getLocale(),
            'priority' => $metabox->getPriority(),
            'screen' => $this->getScreen($metabox->getScreen()),
            'title' => $metabox->getTitle(),
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

    /**
     * Return the metabox fields.
     *
     * @param MetaboxInterface $metabox
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFields(MetaboxInterface $metabox)
    {
        return $this->collection(
            $metabox->repository()->all(),
            function (FieldTypeInterface $field) {
                $field = $field->setResourceTransformerFactory(new Factory());
                $transformer = $field->getResourceTransformerFactory()->make($field->getResourceTransformer());

                /** @var FieldTransformer $transformer */
                return $transformer->transform($field);
            }
        );
    }

    /**
     * Return the metabox groups.
     *
     * @param MetaboxInterface $metabox
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeGroups(MetaboxInterface $metabox)
    {
        return $this->collection(
            $metabox->repository()->getGroups(),
            new GroupTransformer()
        );
    }
}
