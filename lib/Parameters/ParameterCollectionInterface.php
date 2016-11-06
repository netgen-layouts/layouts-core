<?php

namespace Netgen\BlockManager\Parameters;

interface ParameterCollectionInterface
{
    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters();
}
