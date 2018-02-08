<?php

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface;

final class VoterStub implements VoterInterface
{
    /**
     * @var bool|null
     */
    private $vote;

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
     * Returns if the item should be visible. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return bool|null
     */
    public function vote(Item $item)
    {
        return $this->vote;
    }
}
