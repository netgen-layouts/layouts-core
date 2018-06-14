<?php

declare(strict_types=1);

namespace Netgen\BlockManager\HttpCache\Block;

use Netgen\BlockManager\API\Values\Block\Block;
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
    public function setVoters(array $voters = []): void
    {
        $this->voters = array_filter(
            $voters,
            function (VoterInterface $voter): bool {
                return true;
            }
        );
    }

    public function isCacheable(Block $block): bool
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
