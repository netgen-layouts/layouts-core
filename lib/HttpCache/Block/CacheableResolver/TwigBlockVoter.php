<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;

class TwigBlockVoter implements VoterInterface
{
    /**
     * Returns if the block is cacheable. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     *
     * This voter votes NO if the block is a Twig block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool|null
     */
    public function vote(Block $block)
    {
        if (!$block->getDefinition() instanceof TwigBlockDefinitionInterface) {
            return self::ABSTAIN;
        }

        return self::NO;
    }
}
