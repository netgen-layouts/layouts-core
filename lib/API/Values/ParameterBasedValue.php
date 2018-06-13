<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values;

interface ParameterBasedValue
{
    /**
     * Returns all parameters.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters();

    /**
     * Returns the specified parameters.
     *
     * @param string $parameter
     *
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\Parameter
     */
    public function getParameter($parameter);

    /**
     * Returns if the object has a specified parameter.
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function hasParameter($parameter);
}
