<?php

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;
use Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface;
use Netgen\BlockManager\Exception\InvalidInterfaceException;

final class VisibilityResolver implements VisibilityResolverInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface[]
     */
    private $voters = array();

    /**
     * Sets the available voters.
     *
     * @param \Netgen\BlockManager\Collection\Item\VisibilityResolver\VoterInterface[] $voters
     */
    public function setVoters(array $voters = array())
    {
        foreach ($voters as $voter) {
            if (!$voter instanceof VoterInterface) {
                throw new InvalidInterfaceException(
                    'Voter',
                    get_class($voter),
                    VoterInterface::class
                );
            }
        }

        $this->voters = $voters;
    }

    public function isVisible(Item $item)
    {
        foreach ($this->voters as $voter) {
            $result = $voter->vote($item);
            if ($result !== VoterInterface::ABSTAIN) {
                return (bool) $result;
            }
        }

        return true;
    }
}
