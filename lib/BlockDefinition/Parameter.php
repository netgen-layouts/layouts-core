<?php

namespace Netgen\BlockManager\BlockDefinition;

use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Parameter
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * Constructor.
     *
     * @param string $name
     * @param array $options
     */
    public function __construct($name = null, array $options = array())
    {
        $this->name = $name;

        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    /**
     * Returns the parameter name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
