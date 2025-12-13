<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Netgen\Layouts\API\Values\LazyCollection;
use Symfony\Component\Uid\Uuid;

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
     * @return \Symfony\Component\Uid\Uuid[]
     */
    public function getBlockIds(): array
    {
        return array_map(
            static fn (Block $block): Uuid => $block->id,
            $this->getBlocks(),
        );
    }
}
