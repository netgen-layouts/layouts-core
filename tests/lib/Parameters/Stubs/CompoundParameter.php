<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

class CompoundParameter extends Parameter implements CompoundParameterInterface
{
    use ParameterCollectionTrait;

    /**
     * Sets the parameters.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
}
