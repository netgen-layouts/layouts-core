<?php

namespace Netgen\BlockManager\API\Values;

interface BlockUpdateStruct
{
    /**
     * Sets the parameters to the struct.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * Sets the parameter to the struct.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function setParameter($parameterName, $parameterValue);

    /**
     * Returns all parameters from the struct.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Returns the parameter with provided identifier.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If parameter does not exist
     *
     * @return mixed
     */
    public function getParameter($parameterName);

    /**
     * Returns if the struct has a parameter with provided identifier.
     *
     * @param string $parameterName
     *
     * @return bool
     */
    public function hasParameter($parameterName);
}
