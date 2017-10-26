<?php

namespace Netgen\BlockManager\Core\Values;

use Netgen\BlockManager\Exception\Core\ParameterException;

trait ParameterAwareValueTrait
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterValue[]
     */
    protected $parameters = array();

    /**
     * Returns all parameter values.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the specified parameter value.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\Core\ParameterException If the requested parameter does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterValue
     */
    public function getParameter($parameterName)
    {
        if (isset($this->parameters[$parameterName])) {
            return $this->parameters[$parameterName];
        }

        throw ParameterException::noParameter($parameterName);
    }

    /**
     * Returns if the object has a specified parameter value.
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
