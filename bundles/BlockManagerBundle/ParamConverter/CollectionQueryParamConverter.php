<?php

namespace Netgen\Bundle\BlockManagerBundle\ParamConverter;

use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Query;

class CollectionQueryParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     */
    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Returns source attribute name.
     *
     * @return string
     */
    public function getSourceAttributeName()
    {
        return 'collection_query_id';
    }

    /**
     * Returns destination attribute name.
     *
     * @return string
     */
    public function getDestinationAttributeName()
    {
        return 'collectionQuery';
    }

    /**
     * Returns the supported class.
     *
     * @return string
     */
    public function getSupportedClass()
    {
        return Query::class;
    }

    /**
     * Returns the value object.
     *
     * @param int|string $valueId
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\Value
     */
    public function loadValueObject($valueId, $status)
    {
        return $this->collectionService->loadQuery($valueId, $status);
    }
}
