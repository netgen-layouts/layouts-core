<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;

class ContainerVoter implements VoterInterface
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
     * This voter votes NO if the block is a container with a contextual query within it.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool|null
     */
    public function vote(Block $block)
    {
        if (!$block->getDefinition() instanceof ContainerDefinitionInterface) {
            return self::ABSTAIN;
        }

        return $this->isCacheable($block) ? self::ABSTAIN : self::NO;
    }

    /**
     * Returns if the block has a contextual query.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    public function hasContextualQuery(Block $block)
    {
        foreach ($this->blockService->loadCollectionReferences($block) as $collectionReference) {
            $collection = $collectionReference->getCollection();
            if ($collection->hasQuery() && $collection->getQuery()->isContextual()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if the block has a contextual query in one of its placeholders.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    protected function isCacheable(Block $block)
    {
        foreach ($block->getPlaceholders() as $placeholder) {
            foreach ($placeholder as $placeholderBlock) {
                if ($placeholderBlock->getDefinition() instanceof ContainerDefinitionInterface) {
                    if (!$this->isCacheable($placeholderBlock)) {
                        return false;
                    }
                }

                if ($placeholderBlock->getDefinition() instanceof TwigBlockDefinitionInterface) {
                    return false;
                }

                if ($this->hasContextualQuery($placeholderBlock)) {
                    return false;
                }
            }
        }

        return true;
    }
}
