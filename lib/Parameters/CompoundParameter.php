<?php

namespace Netgen\BlockManager\Parameters;

use LogicException;

abstract class CompoundParameter extends Parameter implements CompoundParameterInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    protected $parameters = array();

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
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
            if (!$parameter instanceof ParameterInterface) {
                throw new LogicException('Only parameters can be added to compound parameter.');
            }

            if ($parameter instanceof CompoundParameterInterface) {
                throw new LogicException('Compound parameters cannot be added to a compound parameter.');
            }
        }

        $this->parameters = $parameters;

        parent::__construct($options, $isRequired, $defaultValue, $groups);
    }

    /**
     * Returns the parameters.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
