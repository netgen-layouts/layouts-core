<?php

namespace Netgen\BlockManager\Parameters;

class TranslatableParameterBuilderFactory extends ParameterBuilderFactory
{
    public function createParameterBuilder(array $config = array())
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
