<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Utils\HydratorTrait;

final class ItemViewType
{
    use HydratorTrait;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * Returns the item view type identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the item view type name.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
