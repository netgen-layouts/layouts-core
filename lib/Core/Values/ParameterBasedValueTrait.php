<?php

namespace Netgen\BlockManager\Core\Values;

use Netgen\BlockManager\Exception\Core\ParameterException;

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
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the specified parameter.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\Parameter
     */
    public function getParameter($parameterName)
    {
        if (isset($this->parameters[$parameterName])) {
            return $this->parameters[$parameterName];
        }

        throw ParameterException::noParameter($parameterName);
    }

    /**
     * Returns if the object has a specified parameter.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName)
    {
        return isset($this->parameters[$parameterName]);
    }
}
