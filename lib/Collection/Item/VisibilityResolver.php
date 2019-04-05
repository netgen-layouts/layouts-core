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

    /**
     * Sets the available voters.
     *
     * @param \Netgen\BlockManager\Collection\Item\VisibilityVoterInterface[] $voters
     */
    public function setVoters(array $voters): void
    {
        $this->voters = array_filter(
            $voters,
            static function (VisibilityVoterInterface $voter): bool {
                return true;
            }
        );
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
