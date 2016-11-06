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

    /**
     * Returns the parameter with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($parameterName);

    /**
     * Returns if the parameter with provided name exists in the collection.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName);
}
