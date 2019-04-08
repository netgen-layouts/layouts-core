<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;

final class VisibilityResolver implements VisibilityResolverInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityVoterInterface[]
     */
    private $voters = [];

    public function setVoters(iterable $voters): void
    {
        foreach ($voters as $key => $voter) {
            if ($voter instanceof VisibilityVoterInterface) {
                $this->voters[$key] = $voter;
            }
        }
    }

    public function isVisible(Item $item): bool
    {
        foreach ($this->voters as $voter) {
            $result = $voter->vote($item);
            if ($result !== VisibilityVoterInterface::ABSTAIN) {
                return $result === VisibilityVoterInterface::YES;
            }
        }

        return true;
    }
}
