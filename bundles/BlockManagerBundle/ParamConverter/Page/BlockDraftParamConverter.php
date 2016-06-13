<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Page;

use Netgen\BlockManager\API\Values\Page\BlockDraft;

class BlockDraftParamConverter extends BlockParamConverter
{
    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return BlockDraft::class;
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
        return $this->blockService->loadBlockDraft($values['blockId']);
    }
}
