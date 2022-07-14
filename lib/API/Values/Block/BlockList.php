<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\UuidInterface;

use function array_filter;
use function array_map;

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
                static fn (Block $block): bool => true,
            ),
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
            static fn (Block $block): UuidInterface => $block->getId(),
            $this->getBlocks(),
        );
    }
}
