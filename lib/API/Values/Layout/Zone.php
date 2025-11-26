<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Closure;
use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Zone
{
    use HydratorTrait;
    use LazyPropertyTrait;
    use ValueStatusTrait;

    public private(set) Status $status;

    /**
     * Returns the zone identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns the UUID of the layout to which this zone belongs.
     */
    public private(set) UuidInterface $layoutId;

    /**
     * Returns the linked zone or null if no linked zone exists.
     */
    public private(set) Zone|Closure|null $linkedZone;

    /**
     * Returns if the zone has a linked zone.
     */
    public bool $hasLinkedZone {
        get => $this->getLinkedZone() instanceof self;
    }

    public function getLinkedZone(): ?self
    {
        return $this->getLazyProperty($this->linkedZone);
    }
}
