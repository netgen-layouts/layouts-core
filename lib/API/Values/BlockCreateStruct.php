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
