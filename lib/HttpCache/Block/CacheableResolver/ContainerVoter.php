<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;

/**
 * This voter votes NO if the block is a container with a contextual query within it.
 */
final class ContainerVoter implements VoterInterface
{
    public function vote(Block $block)
    {
        if (!$block->getDefinition() instanceof ContainerDefinitionInterface) {
            return self::ABSTAIN;
        }

        foreach ($block->getPlaceholders() as $placeholder) {
            foreach ($placeholder as $placeholderBlock) {
                if (!$placeholderBlock->isCacheable()) {
                    return self::NO;
                }
            }
        }

        return self::ABSTAIN;
    }
}
