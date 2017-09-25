<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Netgen\BlockManager\API\Values\Collection\Query;

final class ContextualQueryIterator extends QueryIterator
{
    /**
     * @var int
     */
    private $limit;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Query $query
     * @param int $limit
     */
    public function __construct(Query $query, $limit)
    {
        $this->limit = $limit;

        parent::__construct($query);
    }

    public function count()
    {
        $count = $this->query->getInternalLimit();
        if ($count === null || $count > $this->limit) {
            $count = $this->limit;
        }

        return $count;
    }

    protected function buildIterator()
    {
        $queryValues = iterator_to_array(
            $this->generateSlots()
        );

        return new ArrayIterator($queryValues);
    }

    /**
     * Generates a dummy item.
     *
     * @return \Generator
     */
    private function generateSlots()
    {
        for ($i = 0, $count = $this->count(); $i < $count; ++$i) {
            yield new Slot();
        }
    }
}
