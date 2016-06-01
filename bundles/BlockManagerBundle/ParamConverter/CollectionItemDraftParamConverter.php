<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter;

use Netgen\BlockManager\API\Values\Collection\ItemDraft;

class CollectionItemDraftParamConverter extends CollectionItemParamConverter
{
    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return ItemDraft::class;
    }

    /**
     * Returns the value object.
     *
     * @param int|string $valueId
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function loadValueObject($valueId)
    {
        return $this->collectionService->loadItemDraft($valueId);
    }
}
