<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters;

final class TranslatableParameterBuilderFactory extends ParameterBuilderFactory
{
    public function createParameterBuilder(array $config = []): ParameterBuilderInterface
    {
        $config = $this->resolveOptions($config);

        $parameterBuilder = new TranslatableParameterBuilder(
            $this,
            $config['name'],
            $config['type'],
            $config['options'],
            $config['parent']
        );

        return $parameterBuilder;
    }
}
