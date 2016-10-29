<?php

namespace Netgen\BlockManager\Parameters;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ParameterDefinition implements ParameterDefinitionInterface
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
     * @var array
     */
    protected $groups;

    /**
     * Constructor.
     *
     * @param array $options
     * @param bool $isRequired
     * @param mixed $defaultValue
     * @param array $groups
     */
    public function __construct(
        array $options = array(),
        $isRequired = false,
        $defaultValue = null,
        array $groups = array()
    ) {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);

        $this->isRequired = (bool)$isRequired;
        $this->defaultValue = $defaultValue;
        $this->groups = $groups;
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
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
    }
}
