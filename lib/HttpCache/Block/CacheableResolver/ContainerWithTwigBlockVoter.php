<?php

namespace Netgen\BlockManager\HttpCache\Block\CacheableResolver;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\ContainerDefinitionInterface;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;

class ContainerWithTwigBlockVoter implements VoterInterface
{
    /**
     * Returns if the block is cacheable. One of self::YES, self::NO or self::ABSTAIN constants
     * must be returned to indicate the result.
     *
     * This voter votes NO if the block is a container with a Twig block within it.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool|null
     */
    public function vote(Block $block)
    {
        $hasTwigBlock = false;

        if ($block->getDefinition() instanceof ContainerDefinitionInterface) {
            $hasTwigBlock = $this->containerHasTwigBlock($block);
        }

        return $hasTwigBlock ? self::NO : self::ABSTAIN;
    }

    /**
     * Returns if the block has a Twig block in one of its placeholders.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return bool
     */
    protected function containerHasTwigBlock(Block $block)
    {
        foreach ($block->getPlaceholders() as $placeholder) {
            foreach ($placeholder as $placeholderBlock) {
                if ($placeholderBlock->getDefinition() instanceof ContainerDefinitionInterface) {
                    return $this->containerHasTwigBlock($placeholderBlock);
                }

                if ($placeholderBlock->getDefinition() instanceof TwigBlockDefinitionInterface) {
                    return true;
                }
            }
        }

        return false;
    }
}
