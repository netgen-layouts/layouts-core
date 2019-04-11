<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait ParameterTypeTestTrait
{
    /**
     * @var \Netgen\Layouts\Parameters\ParameterTypeInterface
     */
    private $type;

    /**
     * Returns the parameter under test.
     *
     * @param array<string, mixed> $options
     * @param bool $required
     * @param mixed $defaultValue
     *
     * @return \Netgen\Layouts\Parameters\ParameterDefinition
     */
    private function getParameterDefinition(array $options = [], bool $required = false, $defaultValue = null): ParameterDefinition
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

        return ParameterDefinition::fromArray(
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
