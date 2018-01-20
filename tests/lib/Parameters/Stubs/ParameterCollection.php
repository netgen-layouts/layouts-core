<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterCollectionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

final class ParameterCollection implements ParameterCollectionInterface
{
    use ParameterCollectionTrait;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[]|\Closure $parameters
     */
    public function __construct($parameters = null)
    {
        $this->parameters = $parameters;
    }
}
