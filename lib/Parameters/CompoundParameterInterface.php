<?php

namespace Netgen\BlockManager\Parameters;

interface CompoundParameterInterface extends ParameterInterface
{
    /**
     * Returns the parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters();
}
