<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Page;

use Netgen\BlockManager\API\Values\Page\LayoutDraft;

class LayoutDraftParamConverter extends LayoutParamConverter
{
    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return LayoutDraft::class;
    }

    /**
     * Returns the value object.
     *
     * @param array $values
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function loadValueObject(array $values)
    {
        return $this->layoutService->loadLayoutDraft($values['layoutId']);
    }
}
