<?php

namespace Netgen\BlockManager\Tests\HttpCache\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface;

class VoterStub implements VoterInterface
{
    /**
     * @var bool|null
     */
    protected $vote;

    /**
     * Constructor.
     *
     * @param bool|null $vote
     */
    public function __construct($vote)
    {
        $this->vote = $vote;
    }

    /**
     * Returns if the block is cacheable. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool|null
     */
    public function vote(Block $block)
    {
        return $this->vote;
    }
}
