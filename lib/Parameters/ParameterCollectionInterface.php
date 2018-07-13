<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

interface ParameterCollectionInterface
{
    /**
     * Returns all parameters.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters(): array;

    /**
     * Returns the specified parameter.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If the requested parameter does not exist
     */
    public function getParameter(string $parameter): Parameter;

    /**
     * Returns if the object has a specified parameter.
     */
    public function hasParameter(string $parameter): bool;
}
