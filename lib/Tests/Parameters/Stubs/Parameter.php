<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\Parameter as BaseParameter;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Parameter extends BaseParameter
{
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
     * @param string $name
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $type
     * @param array $options
     * @param bool $isRequired
     * @param mixed $defaultValue
     * @param array $groups
     */
    public function __construct(
        $name, ParameterTypeInterface $type,
        array $options = array(),
        $isRequired = false,
        $defaultValue = null,
        array $groups = array()
    ) {
        $this->name = $name;
        $this->type = $type;

        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefined(array('groups', 'default_value', 'required'));

        $optionsResolver->setAllowedTypes('groups', 'array');
        $optionsResolver->setAllowedTypes('required', 'bool');

        $optionsResolver->setDefault('default_value', null);
        $optionsResolver->setDefault('required', false);
        $optionsResolver->setDefault('groups', array());

        $options['required'] = $isRequired;
        $options['groups'] = $groups;

        if ($defaultValue !== null) {
            $options['default_value'] = $defaultValue;
        }

        $this->type->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);

        $this->isRequired = $this->options['required'];
        $this->defaultValue = $this->options['default_value'];
        $this->groups = $this->options['groups'];

        unset($this->options['required'], $this->options['default_value'], $this->options['groups']);
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
}
