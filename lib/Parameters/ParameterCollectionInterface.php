<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

interface ParameterCollectionInterface
{
    /**
     * Returns all parameters from the collection.
     *
     * @return \Netgen\Layouts\Parameters\Parameter[]
     */
    public function getParameters(): array;

    /**
     * Returns the parameter with provided name.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterException If the requested parameter does not exist
     */
    public function getParameter(string $parameterName): Parameter;

    /**
     * Returns if the parameter definition with provided name exists in the collection.
     */
    public function hasParameter(string $parameterName): bool;
}
