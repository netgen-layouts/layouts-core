<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\Parameter as BaseParameter;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Parameter extends BaseParameter
{
    /**
     * Constructor.
     *
     * @param array $properties
     * @param bool $enableTranslatable
     */
    public function __construct(array $properties = array(), $enableTranslatable = false)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefined(array('groups', 'default_value', 'label', 'required'));

        $optionsResolver->setAllowedTypes('groups', 'array');
        $optionsResolver->setAllowedTypes('required', 'bool');
        $optionsResolver->setAllowedTypes('label', array('string', 'null', 'bool'));

        $optionsResolver->setDefault('default_value', null);
        $optionsResolver->setDefault('label', null);
        $optionsResolver->setDefault('required', false);
        $optionsResolver->setDefault('groups', array());

        if ($enableTranslatable) {
            $optionsResolver->setDefined('translatable');
            $optionsResolver->setAllowedTypes('translatable', 'bool');
            $optionsResolver->setDefault('translatable', true);
        }

        $options = array_key_exists('options', $properties) ? $properties['options'] : array();
        $options['required'] = array_key_exists('isRequired', $properties) ? $properties['isRequired'] : false;
        $options['groups'] = array_key_exists('groups', $properties) ? $properties['groups'] : array();
        $options['label'] = array_key_exists('label', $properties) ? $properties['label'] : null;

        $defaultValue = array_key_exists('defaultValue', $properties) ? $properties['defaultValue'] : null;
        if ($defaultValue !== null) {
            $options['default_value'] = $defaultValue;
        }

        if (array_key_exists('type', $properties)) {
            $properties['type']->configureOptions($optionsResolver);
        }

        $options = $optionsResolver->resolve($options);

        $properties['isRequired'] = $options['required'];
        $properties['defaultValue'] = $options['default_value'];
        $properties['label'] = $options['label'];
        $properties['groups'] = $options['groups'];

        unset($options['required'], $options['default_value'], $options['label'], $options['groups']);
        $properties['options'] = $options;

        parent::__construct($properties);
    }
}
