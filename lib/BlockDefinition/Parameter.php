<?php

namespace Netgen\BlockManager\BlockDefinition;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Parameter
{
    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Constructor.
     *
     * @param mixed $defaultValue
     * @param array $options
     */
    public function __construct($defaultValue = null, array $options = array())
    {
        $this->defaultValue = $defaultValue;

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    /**
     * Returns the parameter default value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

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
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    abstract public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * Returns the Symfony form type which matches this parameter.
     *
     * @return string
     */
    abstract public function getFormType();

    /**
     * Maps the parameter options to Symfony form options.
     *
     * @return array
     */
    abstract public function mapFormTypeOptions();
}
