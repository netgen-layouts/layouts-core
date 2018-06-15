<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Values;

use Netgen\BlockManager\Exception\Core\ParameterException;
use Netgen\BlockManager\Parameters\Parameter;

trait ParameterBasedValueTrait
{
    /**
     * @var \Netgen\BlockManager\Parameters\Parameter[]
     */
    protected $parameters = [];

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
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If the requested parameter does not exist
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
