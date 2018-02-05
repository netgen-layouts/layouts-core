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
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]|\Closure $parameterDefinitions
     */
    public function __construct($parameterDefinitions = null)
    {
        $this->parameterDefinitions = $parameterDefinitions;
    }
}
