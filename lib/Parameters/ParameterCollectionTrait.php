<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\InvalidArgumentException;

trait ParameterCollectionTrait
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]|\Closure
     */
    protected $parameters;

    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the parameter with provided name.
     *
     * @param string $parameterName
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If parameter with provided name does not exist
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface
     */
    public function getParameter($parameterName)
    {
        if (isset($this->parameters[$parameterName])) {
            return $this->parameters[$parameterName];
        }

        throw new InvalidArgumentException(
            'parameterName',
            sprintf(
                'Parameter with "%s" name does not exist in the object.',
                $parameterName
            )
        );
    }

    /**
     * Returns if the parameter with provided name exists in the collection.
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
