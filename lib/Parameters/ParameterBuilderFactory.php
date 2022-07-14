<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function is_string;

class ParameterBuilderFactory implements ParameterBuilderFactoryInterface
{
    private ParameterTypeRegistry $parameterTypeRegistry;

    public function __construct(ParameterTypeRegistry $parameterTypeRegistry)
    {
        $this->parameterTypeRegistry = $parameterTypeRegistry;
    }

    public function createParameterBuilder(array $config = []): ParameterBuilderInterface
    {
        $config = $this->resolveOptions($config);

        return new ParameterBuilder(
            $this,
            $config['name'],
            $config['type'],
            $config['options'],
            $config['parent'],
        );
    }

    /**
     * Resolves the provided parameter builder configuration.
     *
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed> $config
     */
    protected function resolveOptions(array $config): array
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefault('name', null);
        $optionsResolver->setDefault('type', null);
        $optionsResolver->setDefault('options', []);
        $optionsResolver->setDefault('parent', null);

        $optionsResolver->setRequired(['name', 'type', 'options', 'parent']);

        $optionsResolver->setAllowedTypes('name', ['null', 'string']);
        $optionsResolver->setAllowedTypes('type', ['null', 'string', ParameterTypeInterface::class]);
        $optionsResolver->setAllowedTypes('options', 'array');
        $optionsResolver->setAllowedTypes('parent', ['null', ParameterBuilderInterface::class]);

        $optionsResolver->setNormalizer(
            'type',
            fn (Options $options, $value) => is_string($value) ?
                    $this->parameterTypeRegistry->getParameterTypeByClass($value) :
                    $value,
        );

        return $optionsResolver->resolve($config);
    }
}
