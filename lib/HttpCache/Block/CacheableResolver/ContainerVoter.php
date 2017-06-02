<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;

class ContainerVoter implements VoterInterface
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
     * Returns if the block is cacheable. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     *
     * This voter votes NO if the block is a container with a contextual query within it.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool|null
     */
    public function vote(Block $block)
    {
        if (!$block->getDefinition() instanceof ContainerDefinitionInterface) {
            return self::ABSTAIN;
        }

        foreach ($block->getPlaceholders() as $placeholder) {
            foreach ($placeholder as $placeholderBlock) {
                if (!$this->cacheableResolver->isCacheable($placeholderBlock)) {
                    return self::NO;
                }
            }
        }

        return self::ABSTAIN;
    }
}
