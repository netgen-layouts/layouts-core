<?php

namespace Netgen\BlockManager\Config\ConfigDefinition\Block;

use Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class HttpCacheConfigHandler implements ConfigDefinitionHandlerInterface
{
    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'use_http_cache',
            ParameterType\Compound\BooleanType::class,
            array(
                'default_value' => false,
            )
        );

        $builder->get('use_http_cache')->add(
            'shared_max_age',
            ParameterType\IntegerType::class,
            array(
                'required' => true,
                'default_value' => 300,
                'min' => 1,
            )
        );
    }
}
