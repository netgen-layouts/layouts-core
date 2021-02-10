<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;

final class VisibilityResolver implements VisibilityResolverInterface
{
    /**
     * @var iterable|\Netgen\Layouts\Collection\Item\VisibilityVoterInterface[]
     */
    private iterable $voters;

    /**
     * @param iterable|\Netgen\Layouts\Collection\Item\VisibilityVoterInterface[] $voters
     */
    public function __construct(iterable $voters)
    {
        $this->voters = $voters;
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
