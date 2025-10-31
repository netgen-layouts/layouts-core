<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;
use Netgen\Layouts\Collection\Item\VisibilityVoterInterface;
use Netgen\Layouts\Collection\Item\VisibilityVoterResult;

final class VoterStub implements VisibilityVoterInterface
{
    public function __construct(
        private VisibilityVoterResult $vote,
    ) {}

    public function vote(Item $item): VisibilityVoterResult
    {
        return $this->vote;
    }
}
