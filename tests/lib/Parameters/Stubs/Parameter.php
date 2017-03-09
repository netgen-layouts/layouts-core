<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\Parameter as BaseParameter;
use Netgen\BlockManager\Parameters\ParameterTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Parameter extends BaseParameter
{
    /**
     * Constructor.
     *
     * @param string $name
     * @param \Netgen\BlockManager\Parameters\ParameterTypeInterface $type
     * @param array $options
     * @param bool $isRequired
     * @param mixed $defaultValue
     * @param array $groups
     * @param string $label
     */
    public function __construct(
        $name,
        ParameterTypeInterface $type,
        array $options = array(),
        $isRequired = false,
        $defaultValue = null,
        array $groups = array(),
        $label = null
    ) {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefined(array('groups', 'default_value', 'label', 'required'));

        $optionsResolver->setAllowedTypes('groups', 'array');
        $optionsResolver->setAllowedTypes('required', 'bool');
        $optionsResolver->setAllowedTypes('label', array('string', 'null', 'bool'));

        $optionsResolver->setDefault('default_value', null);
        $optionsResolver->setDefault('label', null);
        $optionsResolver->setDefault('required', false);
        $optionsResolver->setDefault('groups', array());

        $options['required'] = $isRequired;
        $options['groups'] = $groups;
        $options['label'] = $label;

        if ($defaultValue !== null) {
            $options['default_value'] = $defaultValue;
        }

        $type->configureOptions($optionsResolver);
        $options = $optionsResolver->resolve($options);

        $data = array(
            'name' => $name,
            'type' => $type,
            'isRequired' => $options['required'],
            'defaultValue' => $options['default_value'],
            'label' => $options['label'],
            'groups' => $options['groups'],
        );

        unset($options['required'], $options['default_value'], $options['label'], $options['groups']);
        $data['options'] = $options;

        parent::__construct($data);
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
     * Returns the parameter label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
