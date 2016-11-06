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
        $this->buildParameters();

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
        $this->buildParameters();

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
        $this->buildParameters();

        return isset($this->parameters[$parameterName]);
    }

    /**
     * Builds the parameters from provided closure.
     */
    protected function buildParameters()
    {
        if (is_callable($this->parameters)) {
            $parametersClosure = $this->parameters;
            $this->parameters = $parametersClosure();
        }
    }
}
