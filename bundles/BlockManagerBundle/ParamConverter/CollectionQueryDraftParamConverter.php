<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter;

use Netgen\BlockManager\API\Values\Collection\QueryDraft;

class CollectionQueryDraftParamConverter extends CollectionQueryParamConverter
{
    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return QueryDraft::class;
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
        return $this->collectionService->loadQueryDraft($valueId);
    }
}
