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
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Constructor.
     *
     * @param array $options
     * @param bool $isRequired
     * @param mixed $defaultValue
     */
    public function __construct(array $options = array(), $isRequired = false, $defaultValue = null)
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);

        $this->isRequired = (bool)$isRequired;
        $this->defaultValue = $defaultValue;
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
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getConstraints()
    {
        return array_merge(
            $this->getRequiredConstraints(),
            $this->getValueConstraints()
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
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
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
     * Returns constraints that will be used when parameter is required.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getRequiredConstraints()
    {
        if ($this->isRequired()) {
            return array(
                new Constraints\NotBlank(),
            );
        }

        return array();
    }

    /**
     * Returns constraints that will be used to validate the parameter value.
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getValueConstraints()
    {
        return array();
    }
}
