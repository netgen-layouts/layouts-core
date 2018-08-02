<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityVoterInterface;

final class VoterStub implements VisibilityVoterInterface
{
    /**
     * @var int
     */
    private $vote;

    public function __construct(int $vote)
    {
        $this->vote = $vote;
    }

    public function vote(Item $item): int
    {
        return $this->vote;
    }
}
