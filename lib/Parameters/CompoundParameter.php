<?php

namespace Netgen\BlockManager\Parameters;

use Symfony\Component\Validator\Constraints;

abstract class CompoundParameter extends Parameter implements CompoundParameterInterface
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    protected $parameters = array();

    /**
     * Constructor.
     *
     * @param array $parameters
     * @param array $options
     * @param bool $isRequired
     */
    public function __construct(array $parameters = array(), array $options = array(), $isRequired = false)
    {
        $this->parameters = $parameters;

        parent::__construct($options, $isRequired);
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
