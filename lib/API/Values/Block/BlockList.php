<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Netgen\Layouts\API\Values\LazyCollection;
use Ramsey\Uuid\UuidInterface;

use function array_map;

/**
 * @extends \Netgen\Layouts\API\Values\LazyCollection<int, \Netgen\Layouts\API\Values\Block\Block>
 */
final class BlockList extends LazyCollection
{
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
            static fn (Block $block): UuidInterface => $block->id,
            $this->getBlocks(),
        );
    }
}
