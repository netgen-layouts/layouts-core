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
    protected $attributes = array();

    /**
     * Constructor.
     *
     * @param mixed $defaultValue
     * @param array $attributes
     */
    public function __construct($defaultValue = null, array $attributes = array())
    {
        $this->defaultValue = $defaultValue;

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->attributes = $optionsResolver->resolve($attributes);
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
     * Returns the parameter attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
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
     * Maps the parameter attributes to Symfony form options.
     *
     * @return array
     */
    abstract public function mapFormTypeOptions();
}
