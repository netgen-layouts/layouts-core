<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

class ParameterCollection implements ParameterCollectionInterface
{
    use ParameterCollectionTrait;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }
}
