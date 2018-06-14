<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Type;

use Netgen\BlockManager\Value;

final class Zone extends Value
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
     * @var array
     */
    protected $allowedBlockDefinitions = [];

    /**
     * Returns the zone identifier.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the zone name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns allowed block definition identifiers.
     *
     * @return array
     */
    public function getAllowedBlockDefinitions(): array
    {
        return $this->allowedBlockDefinitions;
    }
}
