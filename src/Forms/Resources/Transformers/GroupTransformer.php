<?php

namespace Themosis\Forms\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Support\Contracts\SectionInterface;

class GroupTransformer extends TransformerAbstract
{
    public function transform(SectionInterface $group)
    {
        return [
            'id' => $group->getId(),
            'theme' => $group->getTheme(),
            'title' => $group->getTitle()
        ];
    }
}
