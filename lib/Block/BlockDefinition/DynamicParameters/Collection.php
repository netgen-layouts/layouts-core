<?php

namespace Netgen\BlockManager\Block\BlockDefinition\DynamicParameters;

class Collection
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var array
     */
    protected $parameterValues;

    /**
     * Constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the requested parameter or null if a parameter does not exist.
     *
     * In case the parameter is a closure, it will be executed before returning the result.
     *
     * @param string $parameterName
     *
     * @return mixed
     */
    public function getParameter($parameterName)
    {
        if (!isset($this->parameters[$parameterName])) {
            return;
        }

        if (!is_callable($this->parameters[$parameterName])) {
            return $this->parameters[$parameterName];
        }

        if (!isset($this->parameterValues[$parameterName])) {
            $this->parameterValues[$parameterName] = $this->parameters[$parameterName]();
        }

        return $this->parameterValues[$parameterName];
    }
}
