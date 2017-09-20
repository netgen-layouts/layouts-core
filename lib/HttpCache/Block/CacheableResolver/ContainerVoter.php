<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface;

/**
 * This voter votes NO if the block is a container with a contextual query within it.
 */
class ContainerVoter implements VoterInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolverInterface
     */
    private $cacheableResolver;

    public function __construct(CacheableResolverInterface $cacheableResolver)
    {
        $this->cacheableResolver = $cacheableResolver;
    }

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
