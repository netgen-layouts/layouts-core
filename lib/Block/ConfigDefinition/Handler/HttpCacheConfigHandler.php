<?php

namespace Netgen\BlockManager\Block\ConfigDefinition\Handler;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use Netgen\BlockManager\Parameters\ParameterType;

class HttpCacheConfigHandler implements ConfigDefinitionHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface
     */
    protected $cacheableResolver;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface $cacheableResolver
     */
    public function __construct(CacheableResolverInterface $cacheableResolver)
    {
        $this->cacheableResolver = $cacheableResolver;
    }

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
                'min' => 0,
            )
        );
    }

    /**
     * Returns if this config definition is enabled for current config aware value.
     *
     * @param \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue
     *
     * @return bool
     */
    public function isEnabled(ConfigAwareValue $configAwareValue)
    {
        if (!$configAwareValue instanceof Block) {
            return false;
        }

        return $this->cacheableResolver->isCacheable($configAwareValue);
    }
}
