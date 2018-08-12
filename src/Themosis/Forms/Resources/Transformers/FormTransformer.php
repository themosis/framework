<?php

namespace Themosis\Forms\Resources\Transformers;

use League\Fractal\TransformerAbstract;
use Themosis\Forms\Contracts\FormInterface;

class FormTransformer extends TransformerAbstract
{
    /**
     * Transform single form.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    public function transform(FormInterface $form)
    {
        return [
            'attributes' => []
        ];
    }
}
