<?php

namespace Netgen\BlockManager\Collection\Result;

use FilterIterator;
use Iterator;
use Netgen\BlockManager\Item\NullItem;

class ResultFilterIterator extends FilterIterator
{
    /**
     * @var int
     */
    protected $flags;

    /**
     * Constructor.
     *
     * @param \Iterator $iterator
     * @param int $flags
     */
    public function __construct(Iterator $iterator, $flags = 0)
    {
        parent::__construct($iterator);

        $this->flags = $flags;
    }

    /**
     * Returns true if result should be included in the result set.
     *
     * @return bool
     */
    public function accept()
    {
        /** @var \Netgen\BlockManager\Collection\Result\Result $result */
        $result = self::current();

        if (!$result->getItem() instanceof NullItem) {
            if ((bool) ($this->flags & ResultSet::INCLUDE_INVISIBLE_ITEMS)) {
                return true;
            }

            return $result->getItem()->isVisible();
        }

        return (bool) ($this->flags & ResultSet::INCLUDE_INVALID_ITEMS);
    }
}
