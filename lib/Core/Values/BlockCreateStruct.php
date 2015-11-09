<?php

namespace Netgen\BlockManager\Core\Values;

use Netgen\BlockManager\API\Values\BlockCreateStruct as APIBlockCreateStruct;

class BlockCreateStruct extends APIBlockCreateStruct
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * Sets the parameters to the struct.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets the parameter to the struct.
     *
     * @param string $parameterName
     * @param mixed $parameterValue
     */
    public function setParameter($parameterName, $parameterValue)
    {
        if ($this->parameters === null) {
            $this->parameters = array();
        }

        $this->parameters[$parameterName] = $parameterValue;
    }

    /**
     * Returns all parameters from the struct.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
