<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Parameters\ParameterType;

use Netgen\Layouts\Parameters\ParameterDefinition;
use Netgen\Layouts\Parameters\ParameterTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait ParameterTypeTestTrait
{
    private ParameterTypeInterface $type;

    /**
     * Returns the parameter under test.
     *
     * @param array<string, mixed> $options
     */
    private function getParameterDefinition(array $options = [], bool $required = false, mixed $defaultValue = null): ParameterDefinition
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->define('required')
            ->required()
            ->default(false);

        $optionsResolver
            ->define('default_value')
            ->required()
            ->default(null);

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
            ],
        );
    }
}
