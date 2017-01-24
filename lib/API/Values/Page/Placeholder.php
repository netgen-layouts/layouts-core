<?php

namespace Netgen\BlockManager\API\Values\Page;

use Netgen\BlockManager\API\Values\ParameterBasedValue;
use Netgen\BlockManager\API\Values\Value;

interface Placeholder extends Value, ParameterBasedValue
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
     * @return \Netgen\BlockManager\API\Values\Page\Block[]
     */
    public function getBlocks();
}
