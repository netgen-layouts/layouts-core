<?php

declare(strict_types=1);

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;

interface VoterInterface
{
    /**
     * Returned by the voter if the block is cacheable.
     */
    const YES = true;

    /**
     * Returned by the voter if the block is not cacheable.
     */
    const NO = false;

    /**
     * Returned by the voter if it cannot decide if the block is cacheable or not.
     */
    const ABSTAIN = null;

    /**
     * Returns if the block is cacheable. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool|null
     */
    public function vote(Block $block);
}
