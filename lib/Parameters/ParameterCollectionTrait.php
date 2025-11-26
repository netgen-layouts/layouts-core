<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Exception\Parameters\ParameterException;

trait ParameterCollectionTrait
{
    private ParameterList $parameters;

    /**
     * Returns all parameters from the collection.
     */
    public function getParameters(): ParameterList
    {
        return new ParameterList($this->parameters->toArray());
    }

    /**
     * Returns the parameter with provided name.
     *
     * @throws \Netgen\Layouts\Exception\Parameters\ParameterException If the requested parameter does not exist
     */
    public function getParameter(string $parameterName): Parameter
    {
        return $this->parameters->get($parameterName) ??
            throw ParameterException::noParameter($parameterName);
    }

    /**
     * Returns if the parameter with provided name exists in the collection.
     */
    public function hasParameter(string $parameterName): bool
    {
        return $this->parameters->containsKey($parameterName);
    }
}
