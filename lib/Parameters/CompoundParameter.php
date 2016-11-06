<?php

namespace Netgen\BlockManager\Parameters;

class CompoundParameter extends Parameter implements CompoundParameterInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    protected $parameters = array();

    /**
     * Constructor.
     *
     * @param string $name
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $type
     * @param array $options
     * @param \Netgen\BlockManager\Parameters\ParameterInterface[] $parameters
     */
    public function __construct(
        $name,
        ParameterTypeInterface $type,
        array $options = array(),
        array $parameters = array()
    ) {
        parent::__construct($name, $type, $options);

        $this->parameters = $parameters;
    }

    /**
     * Returns the list of parameters in the object.
     *
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
