<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\API\Values\Collection\Collection;

class ResultLoader implements ResultLoaderInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultIteratorFactory
     */
    protected $resultIteratorFactory;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\Result\ResultIteratorFactory $resultIteratorFactory
     */
    public function __construct(ResultIteratorFactory $resultIteratorFactory)
    {
        $this->resultIteratorFactory = $resultIteratorFactory;
    }

    /**
     * Loads the result set for provided collection.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Collection $collection
     * @param int $offset
     * @param int $limit
     * @param int $flags
     *
     * @return \Netgen\BlockManager\Collection\Result\ResultSet
     */
    public function load(Collection $collection, $offset = 0, $limit = null, $flags = 0)
    {
        $collectionIterator = new CollectionIterator($collection, $offset, $limit);

        $results = $this->resultIteratorFactory->getResultIterator(
            $collectionIterator,
            $flags
        );

        return new ResultSet(
            array(
                'collection' => $collection,
                'results' => array_values(
                    iterator_to_array($results)
                ),
                'totalCount' => $collectionIterator->count(),
                'offset' => $offset,
                'limit' => $limit,
            )
        );
    }
}
