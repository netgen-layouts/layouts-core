<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;

/**
 * This voter votes NO if the block is contextual.
 */
class ContextualBlockVoter implements VoterInterface
{
    public function vote(Block $block)
    {
        return $block->isContextual() ? self::NO : self::ABSTAIN;
    }
}
