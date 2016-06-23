<?php

namespace Netgen\BlockManager\Parameters;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
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
     * @param array $groups
     *
     * @return array
     */
    public function getConstraints(array $groups = null)
    {
        return array_merge(
            $this->getBaseConstraints($groups),
            $this->getParameterConstraints($groups)
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
     * Returns constraints that are common to all parameters.
     *
     * @param array $groups
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getBaseConstraints(array $groups = null)
    {
        if ($this->isRequired()) {
            return array(
                new Constraints\NotBlank(
                    $this->getBaseConstraintOptions($groups)
                ),
            );
        }

        return array();
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @param array $groups
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getParameterConstraints(array $groups = null)
    {
        return array();
    }

    /**
     * Returns constraint options common to all constraints.
     *
     * @param array $groups
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    protected function getBaseConstraintOptions(array $groups = null)
    {
        $options = array();
        if ($groups !== null) {
            $options['groups'] = $groups;
        }

        return $options;
    }
}
