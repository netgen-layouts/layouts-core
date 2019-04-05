<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;

final class BlockList extends ArrayCollection
{
    public function __construct(array $blocks = [])
    {
        parent::__construct(
            array_filter(
                $blocks,
                static function (Block $block) {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\API\Values\Block\Block[]
     */
    public function getBlocks(): array
    {
        return $this->toArray();
    }

    /**
     * @return int[]|string[]
     */
    public function getBlockIds(): array
    {
        return array_map(
            static function (Block $block) {
                return $block->getId();
            },
            $this->getBlocks()
        );
    }
}
