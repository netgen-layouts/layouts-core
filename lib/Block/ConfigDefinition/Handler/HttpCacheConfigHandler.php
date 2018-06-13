<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\ConfigDefinition\Handler;

use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * This handler specifies the model of HTTP cache configuration within
 * the blocks.
 */
final class HttpCacheConfigHandler implements ConfigDefinitionHandlerInterface
{
    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'use_http_cache',
            ParameterType\BooleanType::class,
            [
                'default_value' => false,
            ]
        );

        $builder->add(
            'shared_max_age',
            ParameterType\IntegerType::class,
            [
                'min' => 0,
            ]
        );
    }
}
