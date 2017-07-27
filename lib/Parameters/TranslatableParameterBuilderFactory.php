<?php

namespace Netgen\BlockManager\Parameters;

class TranslatableParameterBuilderFactory extends ParameterBuilderFactory
{
    /**
     * Returns the new instance of parameter builder.
     *
     * @param array $config
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
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
