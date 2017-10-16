<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Collection\Collection;

final class ResultBuilder implements ResultBuilderInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultIteratorFactory
     */
    private $resultIteratorFactory;

    /**
     * @var \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory
     */
    private $collectionIteratorFactory;

    /**
     * @var int
     */
    private $maxLimit;

    /**
     * @param \Netgen\BlockManager\Collection\Result\ResultIteratorFactory $resultIteratorFactory
     * @param \Netgen\BlockManager\Collection\Result\CollectionIteratorFactory $collectionIteratorFactory
     * @param int $maxLimit
     */
    public function __construct(
        ResultIteratorFactory $resultIteratorFactory,
        CollectionIteratorFactory $collectionIteratorFactory,
        $maxLimit
    ) {
        $this->resultIteratorFactory = $resultIteratorFactory;
        $this->collectionIteratorFactory = $collectionIteratorFactory;
        $this->maxLimit = $maxLimit;
    }

    public function build(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $offset = $offset >= 0 ? $offset : 0;
        if ($limit === null || $limit < 0 || $limit > $this->maxLimit) {
            $limit = $this->maxLimit;
        }

        $collectionIterator = $this->collectionIteratorFactory->getCollectionIterator(
            $collection,
            $offset,
            $limit,
            $flags
        );

        $results = new ArrayIterator();
        $collectionCount = $collectionIterator->count();

        if ($limit > 0 && $offset < $collectionCount) {
            $results = $this->resultIteratorFactory->getResultIterator(
                $collectionIterator,
                $flags
            );
        }

        return new ResultSet(
            array(
                'collection' => $collection,
                'results' => array_values(
                    iterator_to_array($results)
                ),
                'totalCount' => $collectionCount,
                'offset' => $offset,
                'limit' => $limit,
            )
        );
    }
}
