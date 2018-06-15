<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Value;

final class ItemViewType extends Value
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

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
