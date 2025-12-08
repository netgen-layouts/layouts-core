<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;

final class VisibilityResolver implements VisibilityResolverInterface
{
    /**
     * @param iterable<\Netgen\Layouts\Collection\Item\VisibilityVoterInterface> $voters
     */
    public function __construct(
        private iterable $voters,
    ) {}

    public function isVisible(Item $item): bool
    {
        foreach ($this->voters as $voter) {
            $result = $voter->vote($item);
            if ($result !== VisibilityVoterResult::Abstain) {
                return $result === VisibilityVoterResult::Yes;
            }
        }

        return true;
    }
}
