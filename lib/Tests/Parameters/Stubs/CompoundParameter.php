<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\CompoundParameterInterface;

class CompoundParameter extends Parameter implements CompoundParameterInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    protected $parameters;

    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

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
