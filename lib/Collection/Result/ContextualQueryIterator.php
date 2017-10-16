<?php

namespace Netgen\BlockManager\Collection\Result;

use ArrayIterator;

final class ContextualQueryIterator extends QueryIterator
{
    public function count()
    {
        return $this->limit;
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
