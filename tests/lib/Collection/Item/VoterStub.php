<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Item\VisibilityVoterInterface;

final class VoterStub implements VisibilityVoterInterface
{
    private int $vote;

    public function __construct(int $vote)
    {
        $this->vote = $vote;
    }

    public function vote(Item $item): int
    {
        return $this->vote;
    }
}
