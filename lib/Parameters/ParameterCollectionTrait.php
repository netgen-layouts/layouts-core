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
     * Returns all parameters from the collection.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the parameter with provided name.
     *
     * @throws \Netgen\BlockManager\Exception\Parameters\ParameterException If the requested parameter does not exist
     */
    public function getParameter(string $parameterName): Parameter
    {
        if (!$this->hasParameter($parameterName)) {
            throw ParameterException::noParameter($parameterName);
        }

        return $this->parameters[$parameterName];
    }

    /**
     * Returns if the parameter with provided name exists in the collection.
     */
    public function hasParameter(string $parameterName): bool
    {
        return array_key_exists($parameterName, $this->parameters);
    }
}
