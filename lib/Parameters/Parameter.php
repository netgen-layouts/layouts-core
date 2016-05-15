<?php

namespace Netgen\BlockManager\Parameters;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

abstract class Parameter implements ParameterInterface
{
    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var bool
     */
    protected $isRequired;

    /**
     * Constructor.
     *
     * @param array $options
     * @param bool $isRequired
     */
    public function __construct(array $options = array(), $isRequired = false)
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);

        $this->isRequired = (bool)$isRequired;
    }

    /**
     * Returns the parameter type.
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Returns the parameter options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns the parameter constraints.
     *
     * @return array
     */
    public function getConstraints()
    {
        return array_merge(
            $this->getBaseConstraints(),
            $this->getParameterConstraints()
        );
    }

    /**
     * Returns if the parameter is required.
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->isRequired;
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
    }

    /**
     * Returns constraints that are common to all parameters.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getBaseConstraints()
    {
        if ($this->isRequired()) {
            return array(new Constraints\NotBlank());
        }

        return array();
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getParameterConstraints()
    {
        return array();
    }
}
