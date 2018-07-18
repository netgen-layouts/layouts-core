<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

interface ParameterCollectionInterface
{
    /**
     * Returns all parameters from the collection.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters(): array;

    /**
     * Returns the parameter with provided name.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If the requested parameter does not exist
     */
    public function getParameter(string $parameter): Parameter;

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     */
    public function hasParameter(string $parameter): bool;
}
