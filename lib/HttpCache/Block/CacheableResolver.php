<?php

namespace Netgen\BlockManager\HttpCache\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface;

final class CacheableResolver implements CacheableResolverInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface[]
     */
    private $voters = [];

    /**
     * Sets the available voters.
     *
     * @param \Netgen\BlockManager\HttpCache\Block\CacheableResolver\VoterInterface[] $voters
     */
    public function setVoters(array $voters = [])
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

    public function isCacheable(Block $block)
    {
        foreach ($this->voters as $voter) {
            $result = $voter->vote($block);
            if ($result !== VoterInterface::ABSTAIN) {
                return (bool) $result;
            }
        }

        return true;
    }
}
