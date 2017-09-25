<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;

/**
 * This voter votes NO if the block has a collection with contextual query.
 */
final class ContextualQueryVoter implements VoterInterface
{
    public function vote(Block $block)
    {
        return $this->hasContextualQuery($block) ? self::NO : self::ABSTAIN;
    }

    /**
     * Returns if the block has a contextual query.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    private function hasContextualQuery(Block $block)
    {
        foreach ($block->getCollectionReferences() as $collectionReference) {
            $collection = $collectionReference->getCollection();
            if ($collection->hasQuery() && $collection->getQuery()->isContextual()) {
                return true;
            }
        }

        return false;
    }
}
