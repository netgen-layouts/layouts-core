<?php

namespace Netgen\BlockManager\Parameters;

interface CompoundParameterDefinitionInterface extends ParameterDefinitionInterface
{
    /**
     * Returns the parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters();
}
