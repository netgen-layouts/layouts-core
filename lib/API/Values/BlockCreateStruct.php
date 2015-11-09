<?php

namespace Netgen\BlockManager\API\Values;

abstract class BlockCreateStruct extends Value
{
    /**
     * @var string
     */
    public $definitionIdentifier;

    /**
     * @var string
     */
    public $viewType;

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
}
