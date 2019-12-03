<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends \Doctrine\Common\Collections\ArrayCollection<int, \Netgen\Layouts\API\Values\Block\Block>
 */
final class BlockList extends ArrayCollection
{
    /**
     * @param \Netgen\Layouts\API\Values\Block\Block[] $blocks
     */
    public function __construct(array $blocks = [])
    {
        parent::__construct(
            array_filter(
                $blocks,
                static function (Block $block): bool {
                    return true;
                }
            )
        );
    }

    /**
     * @return \Netgen\Layouts\API\Values\Block\Block[]
     */
    public function getBlocks(): array
    {
        return $this->toArray();
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface[]
     */
    public function getBlockIds(): array
    {
        return array_map(
            static function (Block $block): UuidInterface {
                return $block->getId();
            },
            $this->getBlocks()
        );
    }
}
