<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter;

use Netgen\BlockManager\API\Values\Collection\CollectionDraft;

class CollectionDraftParamConverter extends CollectionParamConverter
{
    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return CollectionDraft::class;
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
        return $this->collectionService->loadCollectionDraft($valueId);
    }
}
