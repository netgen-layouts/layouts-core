<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Parameters\ParameterType;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait ParameterTypeTestTrait
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterTypeInterface
     */
    private $type;

    /**
     * Returns the parameter under test.
     *
     * @param array $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\BlockManager\Parameters\ParameterDefinition
     */
    public function getParameterDefinition(array $options = [], $required = false, $defaultValue = null)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setRequired(['required']);
        $optionsResolver->setRequired(['default_value']);
        $optionsResolver->setDefault('required', false);
        $optionsResolver->setDefault('default_value', null);

        $options['required'] = $required;
        if ($defaultValue !== null) {
            $options['default_value'] = $defaultValue;
        }

        $this->type->configureOptions($optionsResolver);
        $options = $optionsResolver->resolve($options);

        $required = $options['required'];
        $defaultValue = $options['default_value'];
        unset($options['required'], $options['default_value']);

        return new ParameterDefinition(
            [
                'name' => 'name',
                'type' => $this->type,
                'options' => $options,
                'isRequired' => $required,
                'defaultValue' => $defaultValue,
            ]
        );
    }
}
