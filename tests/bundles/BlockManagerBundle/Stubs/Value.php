<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Stubs;

use Netgen\BlockManager\API\Values\Value as APIValue;

final class Value implements APIValue
{
    private $valueParams;

    public function __construct($valueParams)
    {
        $this->valueParams = $valueParams;
    }

    /**
     * Returns the status of the value.
     *
     * @return int
     */
    public function getStatus()
    {
    }
}
