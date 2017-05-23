<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;

class ContextualQueryVoter implements VoterInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     */
    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

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
        foreach ($this->blockService->loadCollectionReferences($block) as $collectionReference) {
            $collection = $collectionReference->getCollection();
            if ($collection->hasQuery() && $collection->getQuery()->isContextual()) {
                return self::NO;
            }
        }

        return self::ABSTAIN;
    }
}
