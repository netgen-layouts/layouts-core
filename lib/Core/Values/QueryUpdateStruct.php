<?php

namespace Netgen\BlockManager\Core\Values;

use Netgen\BlockManager\API\Exception\InvalidArgumentException;
use Netgen\BlockManager\API\Values\QueryUpdateStruct as APIQueryUpdateStruct;

class QueryUpdateStruct extends Value implements APIQueryUpdateStruct
{
    /**
     * @var string
     */
    public $identifier;

    /**
     * @var array
     */
    protected $parameters = array();

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

    /**
     * Returns the parameter with provided identifier.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\API\Exception\InvalidArgumentException If parameter does not exist
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!$this->hasParameter($parameterName)) {
            throw new InvalidArgumentException(
                'parameterName',
                'Parameter does not exist in the struct.'
            );
        }

        return $this->parameters[$parameterName];
    }

    /**
     * Returns if the struct has a parameter with provided identifier.
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
