<?php

namespace Netgen\BlockManager\Block\ConfigDefinition\Handler;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

/**
 * This handler specifies the model of HTTP cache configuration within
 * the blocks.
 */
class HttpCacheConfigHandler implements ConfigDefinitionHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface
     */
    private $cacheableResolver;

    public function __construct(CacheableResolverInterface $cacheableResolver)
    {
        $this->cacheableResolver = $cacheableResolver;
    }

    public function buildParameters(ParameterBuilderInterface $builder)
    {
        $builder->add(
            'use_http_cache',
            ParameterType\BooleanType::class,
            array(
                'default_value' => false,
            )
        );

        $builder->add(
            'shared_max_age',
            ParameterType\IntegerType::class,
            array(
                'min' => 0,
            )
        );
    }

    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        if (!$configAwareValue instanceof Block) {
            return false;
        }

        return $this->cacheableResolver->isCacheable($configAwareValue);
    }
}
