<?php

namespace Netgen\BlockManager\Parameters;

use Netgen\BlockManager\Exception\LogicException;

abstract class CompoundParameterDefinition extends ParameterDefinition implements CompoundParameterDefinitionInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    protected $parameters = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[] $parameters
     * @param array $options
     * @param bool $isRequired
     * @param mixed $defaultValue
     * @param array $groups
     */
    public function __construct(
        array $parameters = array(),
        array $options = array(),
        $isRequired = false,
        $defaultValue = null,
        array $groups = array()
    ) {
        foreach ($parameters as $parameter) {
            if (!$parameter instanceof ParameterDefinitionInterface) {
                throw new LogicException('Only parameter definitions can be added to compound parameter definition.');
            }

            if ($parameter instanceof CompoundParameterDefinitionInterface) {
                throw new LogicException('Compound parameters definitions cannot be added to a compound parameter definition.');
            }
        }

        $this->parameters = $parameters;

        parent::__construct($options, $isRequired, $defaultValue, $groups);
    }

    /**
     * Returns the parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinitionInterface[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
