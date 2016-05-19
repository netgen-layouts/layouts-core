<?php

namespace Netgen\BlockManager\API\Values;

abstract class QueryCreateStruct extends AbstractValue
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var string
     */
    public $type;

    /**
     * Sets the parameters to the struct.
     *
     * @param array $parameters
     */
    abstract public function setParameters(array $parameters);

    /**
     * Sets the parameter to the struct.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    abstract public function setParameter($parameterName, $parameterValue);

    /**
     * Returns all parameters from the struct.
     *
     * @return array
     */
    abstract public function getParameters();

    /**
     * Returns the parameter with provided identifier.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If parameter does not exist
     *
     * @return mixed
     */
    abstract public function getParameter($parameterName);

    /**
     * Returns if the struct has a parameter with provided identifier.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    abstract public function hasParameter($parameterName);
}
