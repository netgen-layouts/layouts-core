<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Type;

use Netgen\BlockManager\Value;

final class Zone extends Value
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $allowedBlockDefinitions = [];

    /**
     * Returns the zone identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the zone name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns allowed block definition identifiers.
     */
    public function getAllowedBlockDefinitions(): array
    {
        return $this->allowedBlockDefinitions;
    }
}
