<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;

class ContextualQueryVoter implements VoterInterface
{
    /**
     * Returns if the block is cacheable. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     *
     * This voter votes NO if the block has a collection with contextual query.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool|null
     */
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
    protected function hasContextualQuery(Block $block)
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
