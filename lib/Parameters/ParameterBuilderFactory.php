<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function is_string;

final class ParameterBuilderFactory
{
    public function __construct(
        private ParameterTypeRegistry $parameterTypeRegistry,
    ) {}

    /**
     * Returns the new instance of parameter builder.
     *
     * @param array<string, mixed> $config
     */
    public function createParameterBuilder(
        array $config = [],
        bool $supportsTranslatableParameters = true,
    ): ParameterBuilderInterface {
        $config = $this->resolveOptions($config);

        return new ParameterBuilder(
            $this,
            $config['name'],
            $config['type'],
            $config['options'],
            $config['parent'],
            $supportsTranslatableParameters,
        );
    }

    /**
     * Resolves the provided parameter builder configuration.
     *
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed> $config
     */
    private function resolveOptions(array $config): array
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->define('name')
            ->required()
            ->default(null)
            ->allowedTypes('string', 'null');

        $optionsResolver
            ->define('type')
            ->required()
            ->default(null)
            ->allowedTypes(ParameterTypeInterface::class, 'string', 'null')
            ->normalize(
                fn (Options $options, $value) => is_string($value) ?
                        $this->parameterTypeRegistry->getParameterTypeByClass($value) :
                        $value,
            );

        $optionsResolver
            ->define('options')
            ->required()
            ->default([])
            ->allowedTypes('array');

        $optionsResolver
            ->define('parent')
            ->required()
            ->default(null)
            ->allowedTypes(ParameterBuilderInterface::class, 'null');

        return $optionsResolver->resolve($config);
    }
}
