<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter\Collection;

use Netgen\BlockManager\API\Values\Collection\QueryDraft;

class QueryDraftParamConverter extends QueryParamConverter
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
     * @param array $values
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function loadValueObject(array $values)
    {
        return $this->collectionService->loadQueryDraft($values['queryId']);
    }
}
