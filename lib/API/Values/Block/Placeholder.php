<?php

namespace Netgen\BlockManager\API\Values\Block;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\API\Values\Value;

interface Placeholder extends Value, ParameterBasedValue, ArrayAccess, IteratorAggregate, Countable
{
    /**
     * Returns the placeholder identifier.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns all blocks in this placeholder.
     *
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function getBlocks();
}
