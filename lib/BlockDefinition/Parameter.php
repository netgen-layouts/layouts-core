<?php

namespace Netgen\BlockManager\BlockDefinition;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Parameter
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Constructor.
     *
     * @param string $identifier
     * @param string $name
     * @param array $attributes
     * @param mixed $defaultValue
     */
    public function __construct($identifier, $name, $attributes = array(), $defaultValue = null)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->defaultValue = $defaultValue;

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->attributes = $optionsResolver->resolve($attributes);
    }

    /**
     * Returns the parameter type.
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Configures the options for this parameter
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    abstract public function configureOptions(OptionsResolver $optionsResolver);

    /**
     * Returns the Symfony form type which matches this parameter
     *
     * @return string
     */
    abstract public function getFormType();

    /**
     * Maps the parameter attributes to Symfony form options
     *
     * @return array
     */
    abstract public function mapFormTypeOptions();

    /**
     * Returns the parameter identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the parameter human readable name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Returns the parameter default value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
