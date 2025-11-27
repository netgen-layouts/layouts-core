<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Type;

use Netgen\Layouts\Utils\HydratorTrait;

final class Zone
{
    use HydratorTrait;

    /**
     * Returns the zone identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns the zone name.
     */
    public private(set) string $name;

    /**
     * Returns allowed block definition identifiers.
     *
     * @var string[]
     */
    public private(set) array $allowedBlockDefinitions = [];
}
