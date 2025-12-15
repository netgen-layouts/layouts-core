<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use Netgen\Layouts\Utils\HydratorTrait;

/**
 * Placeholder represents a set of blocks inside a container block.
 *
 * Each container block can have multiple placeholders, allowing to render
 * each block set separately.
 */
final class Placeholder
{
    use HydratorTrait;

    /**
     * Returns the placeholder identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns all blocks in this placeholder.
     */
    public private(set) BlockList $blocks {
        get => BlockList::fromArray($this->blocks->toArray());
    }
}
