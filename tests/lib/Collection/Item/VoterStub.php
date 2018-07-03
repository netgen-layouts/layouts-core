<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityVoterInterface;

final class VoterStub implements VisibilityVoterInterface
{
    /**
     * @var bool|null
     */
    private $vote;

    public function __construct(?bool $vote)
    {
        $this->vote = $vote;
    }

    public function vote(Item $item): ?bool
    {
        return $this->vote;
    }
}
