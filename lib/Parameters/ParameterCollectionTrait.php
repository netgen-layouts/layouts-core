<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\Parameters\ParameterException;

trait ParameterCollectionTrait
{
    /**
     * @var \Netgen\BlockManager\Parameters\Parameter[]
     */
    private $parameters = [];

    /**
     * Returns all parameters.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the specified parameter.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If the requested parameter does not exist
     */
    public function getParameter(string $parameterName): Parameter
    {
        if (isset($this->parameters[$parameterName])) {
            return $this->parameters[$parameterName];
        }

        throw ParameterException::noParameter($parameterName);
    }

    /**
     * Returns if the object has a specified parameter.
     */
    public function hasParameter(string $parameterName): bool
    {
        return isset($this->parameters[$parameterName]);
    }
}
