<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Type;

use Netgen\Layouts\Utils\HydratorTrait;

final class Zone
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
