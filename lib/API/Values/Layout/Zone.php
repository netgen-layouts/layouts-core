<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Netgen\Layouts\API\Values\LazyPropertyTrait;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Zone
{
    use HydratorTrait;
    use LazyPropertyTrait;
    use ValueStatusTrait;

    private string $identifier;

    private UuidInterface $layoutId;

    /**
     * @var \Netgen\Layouts\API\Values\Layout\Zone|\Closure|null
     */
    private $linkedZone;

    /**
     * Returns the zone identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns the UUID of the layout to which this zone belongs.
     */
    public function getLayoutId(): UuidInterface
    {
        return $this->layoutId;
    }

    /**
     * Returns if the zone has a linked zone.
     */
    public function hasLinkedZone(): bool
    {
        return $this->getLinkedZone() instanceof self;
    }

    /**
     * Returns the linked zone or null if no linked zone exists.
     */
    public function getLinkedZone(): ?self
    {
        return $this->getLazyProperty($this->linkedZone);
    }
}
