<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;
use Countable;
use IteratorIterator;

final class ContextualQueryIterator extends IteratorIterator implements Countable
{
    /**
     * @var int
     */
    private $limit;

    /**
     * Constructor.
     *
     *   @param int $limit
     */
    public function __construct($limit = null)
    {
        $this->limit = $limit;

        parent::__construct($this->buildIterator());
    }

    public function count()
    {
        return $this->limit;
    }

    protected function buildIterator()
    {
        $queryValues = iterator_to_array($this->generateSlots());

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
